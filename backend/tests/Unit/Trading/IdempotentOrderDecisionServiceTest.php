<?php

namespace Tests\Unit\Trading;

use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use App\Domain\Financial\Persistence\ConcurrencyGuard;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\Enums\OrderDecision;
use App\Domain\Trading\Enums\OrderStatus;
use App\Domain\Trading\Order\IdempotentOrderDecisionService;
use App\Domain\Trading\Order\Order;
use App\Domain\Trading\Order\OrderDecisionCommand;
use App\Domain\Trading\Quote\Quote;
use App\Domain\Trading\Validation\OrderValidationEngine;
use App\Domain\Trading\Validation\Rules\OrderQuoteConsistencyRule;
use App\Domain\Trading\Validation\Rules\SubmittedOrderRule;
use App\Infrastructure\Financial\InMemory\InMemoryTenantScopedIdempotencyRegistry;
use App\Infrastructure\Trading\InMemory\InMemoryTenantScopedOrderRepository;
use App\Infrastructure\Trading\InMemory\InMemoryTenantScopedQuoteRepository;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IdempotentOrderDecisionServiceTest extends TestCase
{
    #[Test]
    public function approval_is_validated_persisted_and_replayed_idempotently(): void
    {
        [$service, $orders, $scope, $order] = $this->fixture();
        $command = new OrderDecisionCommand(
            $scope,
            $order->id(),
            OrderDecision::APPROVE,
            TraceId::generate(),
            IdempotencyKey::fromString('approve-command-1'),
            'approve-hash-1',
        );

        $approved = $service->decide($command);
        $replayed = $service->decide($command);

        self::assertSame(OrderStatus::APPROVED, $approved->status());
        self::assertSame(OrderStatus::APPROVED, $replayed->status());
        self::assertTrue($approved->id()->equals($replayed->id()));
        self::assertSame(OrderStatus::APPROVED, $orders->find($scope, $order->id())?->status());
    }

    #[Test]
    public function same_idempotency_key_cannot_be_reused_for_a_different_decision_request(): void
    {
        [$service, , $scope, $order] = $this->fixture();
        $key = IdempotencyKey::fromString('decision-conflict');

        $service->decide(new OrderDecisionCommand(
            $scope,
            $order->id(),
            OrderDecision::APPROVE,
            TraceId::generate(),
            $key,
            'hash-a',
        ));

        $this->expectException(DomainException::class);
        $service->decide(new OrderDecisionCommand(
            $scope,
            $order->id(),
            OrderDecision::APPROVE,
            TraceId::generate(),
            $key,
            'hash-b',
        ));
    }

    #[Test]
    public function submitted_order_can_be_rejected_with_a_reason(): void
    {
        [$service, , $scope, $order] = $this->fixture();

        $rejected = $service->decide(new OrderDecisionCommand(
            $scope,
            $order->id(),
            OrderDecision::REJECT,
            TraceId::generate(),
            IdempotencyKey::fromString('reject-command-1'),
            'reject-hash-1',
            'Operator rejected the order.',
        ));

        self::assertSame(OrderStatus::REJECTED, $rejected->status());
        self::assertSame('Operator rejected the order.', $rejected->rejectionReason());
    }

    #[Test]
    public function order_with_mismatched_quote_correlation_is_rejected_by_validation_pipeline(): void
    {
        [$service, $orders, $scope, $order] = $this->fixture();
        $invalid = Order::rehydrate(
            $order->id(),
            $order->quoteId(),
            $order->scope(),
            $order->traceId(),
            CorrelationId::generate(),
            $order->idempotencyKey(),
            OrderStatus::SUBMITTED,
            $order->createdAt(),
            $order->submittedAt(),
        );
        $orders->save($scope, $invalid);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('trading.order.correlation_mismatch');

        $service->decide(new OrderDecisionCommand(
            $scope,
            $invalid->id(),
            OrderDecision::APPROVE,
            TraceId::generate(),
            IdempotencyKey::fromString('invalid-correlation-command'),
            'invalid-correlation-hash',
        ));
    }

    /** @return array{IdempotentOrderDecisionService, InMemoryTenantScopedOrderRepository, FinancialScope, Order} */
    private function fixture(): array
    {
        $scope = new FinancialScope('tenant-a', 'company-a', 'branch-a');
        $quote = Quote::request(
            $scope,
            TraceId::generate(),
            CorrelationId::generate(),
            IdempotencyKey::fromString('quote-request'),
            new DateTimeImmutable('2026-08-04T18:00:00+00:00'),
        )->freeze(new DateTimeImmutable('2026-08-04T18:05:00+00:00'))
            ->use(new DateTimeImmutable('2026-08-04T18:01:00+00:00'));

        $order = Order::draftFromUsedQuote(
            $quote,
            TraceId::generate(),
            IdempotencyKey::fromString('order-create'),
            new DateTimeImmutable('2026-08-04T18:01:10+00:00'),
        )->submit(new DateTimeImmutable('2026-08-04T18:01:20+00:00'));

        $orders = new InMemoryTenantScopedOrderRepository();
        $quotes = new InMemoryTenantScopedQuoteRepository();
        $orders->save($scope, $order);
        $quotes->save($scope, $quote);

        $atomic = new class implements AtomicFinancialOperation {
            public function execute(callable $operation): mixed { return $operation(); }
        };
        $concurrency = new class implements ConcurrencyGuard {
            public function synchronized(string $resource, callable $operation): mixed { return $operation(); }
        };

        $service = new IdempotentOrderDecisionService(
            $orders,
            $quotes,
            new OrderValidationEngine([
                new OrderQuoteConsistencyRule(),
                new SubmittedOrderRule(),
            ]),
            new InMemoryTenantScopedIdempotencyRegistry(),
            $concurrency,
            $atomic,
        );

        return [$service, $orders, $scope, $order];
    }
}
