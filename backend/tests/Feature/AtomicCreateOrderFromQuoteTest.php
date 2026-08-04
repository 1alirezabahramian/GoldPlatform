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
use App\Domain\Trading\Enums\QuoteStatus;
use App\Domain\Trading\Order\CreateOrderFromQuoteCommand;
use App\Domain\Trading\Order\IdempotentCreateOrderFromQuoteService;
use App\Domain\Trading\Quote\Quote;
use App\Domain\Trading\ValueObjects\QuoteId;
use DateTimeImmutable;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AtomicCreateOrderFromQuoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_atomically_uses_quote_and_creates_one_draft_order(): void
    {
        [$scope, $quote, $requestedAt] = $this->storeFrozenQuote();
        $command = $this->command($scope, $quote->id(), 'create-order-1', 'hash-1', $requestedAt);

        $service = $this->app->make(IdempotentCreateOrderFromQuoteService::class);
        $created = $service->execute($command);
        $replayed = $service->execute($command);

        self::assertSame($created->id()->value(), $replayed->id()->value());
        self::assertSame(OrderStatus::DRAFT, $created->status());
        self::assertSame(QuoteStatus::USED, $this->app->make(TenantScopedQuoteRepository::class)
            ->find($scope, $quote->id())?->status());
        self::assertSame($created->id()->value(), $this->app->make(TenantScopedOrderRepository::class)
            ->findByQuote($scope, $quote->id())?->id()->value());

        $events = $this->app->make(TenantScopedFinancialEventStore::class)
            ->byCorrelationId($scope, $quote->correlationId());
        self::assertCount(1, $events);
        self::assertSame('trading.order.created-from-quote', $events[0]->name());
    }

    public function test_same_idempotency_key_with_different_request_is_rejected(): void
    {
        [$scope, $quote, $requestedAt] = $this->storeFrozenQuote();
        $service = $this->app->make(IdempotentCreateOrderFromQuoteService::class);
        $service->execute($this->command($scope, $quote->id(), 'same-key', 'hash-a', $requestedAt));

        $this->expectException(DomainException::class);
        $service->execute($this->command($scope, $quote->id(), 'same-key', 'hash-b', $requestedAt));
    }

    public function test_different_idempotency_keys_cannot_create_two_orders_for_one_quote(): void
    {
        [$scope, $quote, $requestedAt] = $this->storeFrozenQuote();
        $service = $this->app->make(IdempotentCreateOrderFromQuoteService::class);
        $service->execute($this->command($scope, $quote->id(), 'first-key', 'first-hash', $requestedAt));

        $this->expectException(DomainException::class);
        $service->execute($this->command($scope, $quote->id(), 'second-key', 'second-hash', $requestedAt));
    }

    /** @return array{FinancialScope, Quote, DateTimeImmutable} */
    private function storeFrozenQuote(): array
    {
        $scope = new FinancialScope('tenant-a', 'company-a', 'branch-a');
        $requestedAt = new DateTimeImmutable('2026-08-05T00:00:00+00:00');
        $quote = Quote::request(
            scope: $scope,
            traceId: TraceId::generate(),
            correlationId: CorrelationId::generate(),
            idempotencyKey: IdempotencyKey::fromString('quote-request'),
            requestedAt: $requestedAt,
        )->freeze($requestedAt->modify('+5 minutes'));

        $this->app->make(TenantScopedQuoteRepository::class)->save($scope, $quote);

        return [$scope, $quote, $requestedAt];
    }

    private function command(
        FinancialScope $scope,
        QuoteId $quoteId,
        string $key,
        string $hash,
        DateTimeImmutable $requestedAt,
    ): CreateOrderFromQuoteCommand {
        return new CreateOrderFromQuoteCommand(
            scope: $scope,
            quoteId: $quoteId,
            traceId: TraceId::generate(),
            idempotencyKey: IdempotencyKey::fromString($key),
            requestHash: $hash,
            usedAt: $requestedAt->modify('+1 minute'),
            createdAt: $requestedAt->modify('+1 minute'),
        );
    }
}
