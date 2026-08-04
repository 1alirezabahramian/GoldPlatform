<?php

namespace Tests\Feature;

use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\Contracts\TenantScopedOrderRepository;
use App\Domain\Trading\Contracts\TenantScopedQuoteRepository;
use App\Domain\Trading\Enums\OrderStatus;
use App\Domain\Trading\Enums\QuoteStatus;
use App\Domain\Trading\Order\Order;
use App\Domain\Trading\Quote\Quote;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TradingDatabaseRepositoriesIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_container_resolves_database_trading_repositories(): void
    {
        self::assertSame(
            'App\\Infrastructure\\Trading\\Database\\DatabaseTenantScopedQuoteRepository',
            $this->app->make(TenantScopedQuoteRepository::class)::class,
        );
        self::assertSame(
            'App\\Infrastructure\\Trading\\Database\\DatabaseTenantScopedOrderRepository',
            $this->app->make(TenantScopedOrderRepository::class)::class,
        );
    }

    public function test_quote_to_order_round_trip_is_tenant_scoped(): void
    {
        $scope = new FinancialScope('tenant-a', 'company-a', 'branch-a');
        $otherScope = new FinancialScope('tenant-b', 'company-a', 'branch-a');
        $requestedAt = new DateTimeImmutable('2026-08-04T20:00:00+00:00');

        $quote = Quote::request(
            scope: $scope,
            traceId: TraceId::generate(),
            correlationId: CorrelationId::generate(),
            idempotencyKey: IdempotencyKey::fromString('quote-request-a'),
            requestedAt: $requestedAt,
        )->freeze($requestedAt->modify('+5 minutes'))
          ->use($requestedAt->modify('+1 minute'));

        $quotes = $this->app->make(TenantScopedQuoteRepository::class);
        $orders = $this->app->make(TenantScopedOrderRepository::class);
        $quotes->save($scope, $quote);

        $restoredQuote = $quotes->find($scope, $quote->id());
        self::assertNotNull($restoredQuote);
        self::assertSame(QuoteStatus::USED, $restoredQuote->status());
        self::assertNull($quotes->find($otherScope, $quote->id()));

        $order = Order::draftFromUsedQuote(
            quote: $restoredQuote,
            traceId: TraceId::generate(),
            idempotencyKey: IdempotencyKey::fromString('order-create-a'),
            createdAt: $requestedAt->modify('+1 minute'),
        )->submit($requestedAt->modify('+2 minutes'))
          ->approve();

        $orders->save($scope, $order);

        $restoredOrder = $orders->find($scope, $order->id());
        self::assertNotNull($restoredOrder);
        self::assertSame(OrderStatus::APPROVED, $restoredOrder->status());
        self::assertTrue($restoredOrder->quoteId()->equals($quote->id()));
        self::assertSame($quote->correlationId()->value(), $restoredOrder->correlationId()->value());
        self::assertNull($orders->find($otherScope, $order->id()));
        self::assertNotNull($orders->findByQuote($scope, $quote->id()));
        self::assertNull($orders->findByQuote($otherScope, $quote->id()));
    }

    public function test_order_cannot_be_saved_until_its_quote_exists_in_the_same_scope(): void
    {
        $scope = new FinancialScope('tenant-a');
        $requestedAt = new DateTimeImmutable('2026-08-04T20:00:00+00:00');
        $quote = Quote::request(
            scope: $scope,
            traceId: TraceId::generate(),
            correlationId: CorrelationId::generate(),
            idempotencyKey: IdempotencyKey::fromString('missing-quote'),
            requestedAt: $requestedAt,
        )->freeze($requestedAt->modify('+5 minutes'))
          ->use($requestedAt->modify('+1 minute'));

        $order = Order::draftFromUsedQuote(
            quote: $quote,
            traceId: TraceId::generate(),
            idempotencyKey: IdempotencyKey::fromString('missing-order'),
            createdAt: $requestedAt->modify('+1 minute'),
        );

        $this->expectException(\DomainException::class);
        $this->app->make(TenantScopedOrderRepository::class)->save($scope, $order);
    }
}
