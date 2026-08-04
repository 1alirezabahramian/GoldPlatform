<?php

namespace Tests\Feature;

use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\Contracts\TenantScopedOrderRepository;
use App\Domain\Trading\Contracts\TenantScopedQuoteRepository;
use App\Domain\Trading\Enums\OrderStatus;
use App\Domain\Trading\Order\IdempotentSubmitOrderService;
use App\Domain\Trading\Order\Order;
use App\Domain\Trading\Order\SubmitOrderCommand;
use App\Domain\Trading\Quote\Quote;
use DateTimeImmutable;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class IdempotentSubmitOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_submits_draft_order_atomically_and_replays_same_result(): void
    {
        [$scope, $order, $submittedAt] = $this->storeDraftOrder();
        $command = $this->command($scope, $order, 'submit-key', 'submit-hash', $submittedAt);
        $service = $this->app->make(IdempotentSubmitOrderService::class);

        $submitted = $service->execute($command);
        $replayed = $service->execute($command);

        self::assertSame(OrderStatus::SUBMITTED, $submitted->status());
        self::assertSame($submitted->id()->value(), $replayed->id()->value());
        self::assertSame(
            OrderStatus::SUBMITTED,
            $this->app->make(TenantScopedOrderRepository::class)->find($scope, $order->id())?->status(),
        );

        $events = $this->app->make(TenantScopedFinancialEventStore::class)
            ->byCorrelationId($scope, $order->correlationId());
        self::assertCount(1, $events);
        self::assertSame('trading.order.submitted', $events[0]->name());
    }

    public function test_same_key_with_different_submission_request_is_rejected(): void
    {
        [$scope, $order, $submittedAt] = $this->storeDraftOrder();
        $service = $this->app->make(IdempotentSubmitOrderService::class);
        $service->execute($this->command($scope, $order, 'same-submit-key', 'hash-a', $submittedAt));

        $this->expectException(DomainException::class);
        $service->execute($this->command($scope, $order, 'same-submit-key', 'hash-b', $submittedAt));
    }

    public function test_submitted_order_cannot_be_submitted_again_with_another_key(): void
    {
        [$scope, $order, $submittedAt] = $this->storeDraftOrder();
        $service = $this->app->make(IdempotentSubmitOrderService::class);
        $service->execute($this->command($scope, $order, 'first-submit-key', 'first-submit-hash', $submittedAt));

        $this->expectException(DomainException::class);
        $service->execute($this->command(
            $scope,
            $order,
            'second-submit-key',
            'second-submit-hash',
            $submittedAt->modify('+1 second'),
        ));
    }

    /** @return array{FinancialScope, Order, DateTimeImmutable} */
    private function storeDraftOrder(): array
    {
        $scope = new FinancialScope('tenant-a', 'company-a', 'branch-a');
        $requestedAt = new DateTimeImmutable('2026-08-05T00:00:00+00:00');
        $quote = Quote::request(
            scope: $scope,
            traceId: TraceId::generate(),
            correlationId: CorrelationId::generate(),
            idempotencyKey: IdempotencyKey::fromString('submit-quote-request'),
            requestedAt: $requestedAt,
        )->freeze($requestedAt->modify('+5 minutes'))
          ->use($requestedAt->modify('+1 minute'));

        $order = Order::draftFromUsedQuote(
            quote: $quote,
            traceId: TraceId::generate(),
            idempotencyKey: IdempotencyKey::fromString('submit-order-create'),
            createdAt: $requestedAt->modify('+1 minute'),
        );

        $this->app->make(TenantScopedQuoteRepository::class)->save($scope, $quote);
        $this->app->make(TenantScopedOrderRepository::class)->save($scope, $order);

        return [$scope, $order, $requestedAt->modify('+2 minutes')];
    }

    private function command(
        FinancialScope $scope,
        Order $order,
        string $key,
        string $hash,
        DateTimeImmutable $submittedAt,
    ): SubmitOrderCommand {
        return new SubmitOrderCommand(
            scope: $scope,
            orderId: $order->id(),
            traceId: TraceId::generate(),
            idempotencyKey: IdempotencyKey::fromString($key),
            requestHash: $hash,
            submittedAt: $submittedAt,
        );
    }
}
