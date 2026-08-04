<?php

namespace Tests\Unit\Trading;

use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\Enums\OrderStatus;
use App\Domain\Trading\Order\Order;
use App\Domain\Trading\Quote\Quote;
use App\Infrastructure\Trading\InMemory\InMemoryTenantScopedOrderRepository;
use DateTimeImmutable;
use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class OrderLifecycleTest extends TestCase
{
    public function test_used_quote_creates_draft_order_that_can_be_submitted_and_approved(): void
    {
        $createdAt = new DateTimeImmutable('2026-08-04T18:00:00+00:00');
        $order = $this->draftOrder($this->scope('tenant-a'), $createdAt);

        self::assertSame(OrderStatus::DRAFT, $order->status());

        $submitted = $order->submit($createdAt->modify('+1 second'));
        $approved = $submitted->approve();

        self::assertSame(OrderStatus::SUBMITTED, $submitted->status());
        self::assertSame(OrderStatus::APPROVED, $approved->status());
        self::assertTrue($order->correlationId()->equals($approved->correlationId()));
    }

    public function test_order_cannot_be_created_from_quote_that_has_not_been_used(): void
    {
        $scope = $this->scope('tenant-a');
        $requestedAt = new DateTimeImmutable('2026-08-04T18:00:00+00:00');
        $quote = Quote::request(
            $scope,
            TraceId::generate(),
            CorrelationId::generate(),
            IdempotencyKey::fromString('quote-request'),
            $requestedAt,
        );

        $this->expectException(DomainException::class);

        Order::draftFromUsedQuote(
            $quote,
            TraceId::generate(),
            IdempotencyKey::fromString('order-create'),
            $requestedAt,
        );
    }

    public function test_submitted_order_can_be_rejected_only_with_a_reason(): void
    {
        $createdAt = new DateTimeImmutable('2026-08-04T18:00:00+00:00');
        $submitted = $this->draftOrder($this->scope('tenant-a'), $createdAt)
            ->submit($createdAt->modify('+1 second'));

        $rejected = $submitted->reject('Price confirmation was declined.');

        self::assertSame(OrderStatus::REJECTED, $rejected->status());
        self::assertSame('Price confirmation was declined.', $rejected->rejectionReason());

        $this->expectException(InvalidArgumentException::class);
        $submitted->reject('   ');
    }

    public function test_terminal_order_cannot_be_approved_or_cancelled_again(): void
    {
        $createdAt = new DateTimeImmutable('2026-08-04T18:00:00+00:00');
        $approved = $this->draftOrder($this->scope('tenant-a'), $createdAt)
            ->submit($createdAt->modify('+1 second'))
            ->approve();

        $this->expectException(DomainException::class);
        $approved->cancel();
    }

    public function test_repository_isolates_orders_by_scope_and_finds_by_quote(): void
    {
        $repository = new InMemoryTenantScopedOrderRepository();
        $scopeA = $this->scope('tenant-a');
        $scopeB = $this->scope('tenant-b');
        $order = $this->draftOrder($scopeA, new DateTimeImmutable('2026-08-04T18:00:00+00:00'));

        $repository->save($scopeA, $order);

        self::assertSame($order, $repository->find($scopeA, $order->id()));
        self::assertNull($repository->find($scopeB, $order->id()));
        self::assertSame($order, $repository->findByQuote($scopeA, $order->quoteId()));
        self::assertNull($repository->findByQuote($scopeB, $order->quoteId()));
    }

    private function draftOrder(FinancialScope $scope, DateTimeImmutable $createdAt): Order
    {
        $quote = Quote::request(
            $scope,
            TraceId::generate(),
            CorrelationId::generate(),
            IdempotencyKey::fromString('quote-'.$scope->key()),
            $createdAt->modify('-10 seconds'),
        )
            ->freeze($createdAt->modify('+30 seconds'))
            ->use($createdAt->modify('-1 second'));

        return Order::draftFromUsedQuote(
            $quote,
            TraceId::generate(),
            IdempotencyKey::fromString('order-'.$scope->key()),
            $createdAt,
        );
    }

    private function scope(string $tenantId): FinancialScope
    {
        return new FinancialScope($tenantId, 'company-a', 'branch-a');
    }
}
