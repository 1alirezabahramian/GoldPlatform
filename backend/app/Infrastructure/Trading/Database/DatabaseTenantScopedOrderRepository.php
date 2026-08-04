<?php

namespace App\Infrastructure\Trading\Database;

use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\Contracts\TenantScopedOrderRepository;
use App\Domain\Trading\Enums\OrderStatus;
use App\Domain\Trading\Order\Order;
use App\Domain\Trading\ValueObjects\OrderId;
use App\Domain\Trading\ValueObjects\QuoteId;
use DateTimeImmutable;
use DomainException;
use Illuminate\Support\Facades\DB;

final class DatabaseTenantScopedOrderRepository implements TenantScopedOrderRepository
{
    public function save(FinancialScope $scope, Order $order): void
    {
        if ($order->scope()->key() !== $scope->key()) {
            throw new DomainException('Order scope does not match repository scope.');
        }

        $scopeHash = hash('sha256', $scope->key());
        $quoteId = DB::table('trading_quotes')
            ->where('scope_hash', $scopeHash)
            ->where('quote_uuid', $order->quoteId()->value())
            ->value('id');

        if ($quoteId === null) {
            throw new DomainException('Order quote cannot be resolved in this trading scope.');
        }

        DB::table('trading_orders')->updateOrInsert(
            [
                'scope_hash' => $scopeHash,
                'order_uuid' => $order->id()->value(),
            ],
            [
                'trading_quote_id' => $quoteId,
                'scope_key' => $scope->key(),
                'tenant_id' => $scope->tenantId(),
                'company_id' => $scope->companyId(),
                'branch_id' => $scope->branchId(),
                'trace_id' => $order->traceId()->value(),
                'correlation_id' => $order->correlationId()->value(),
                'idempotency_key' => $order->idempotencyKey()->value(),
                'status' => $order->status()->value,
                'created_at' => $order->createdAt()->format('Y-m-d H:i:s.u'),
                'submitted_at' => $order->submittedAt()?->format('Y-m-d H:i:s.u'),
                'rejection_reason' => $order->rejectionReason(),
                'updated_at' => now(),
            ],
        );
    }

    public function find(FinancialScope $scope, OrderId $orderId): ?Order
    {
        $row = DB::table('trading_orders as orders')
            ->join('trading_quotes as quotes', 'quotes.id', '=', 'orders.trading_quote_id')
            ->where('orders.scope_hash', hash('sha256', $scope->key()))
            ->where('orders.order_uuid', $orderId->value())
            ->select('orders.*', 'quotes.quote_uuid')
            ->first();

        return $row === null ? null : $this->rehydrate($scope, $row);
    }

    public function findByQuote(FinancialScope $scope, QuoteId $quoteId): ?Order
    {
        $row = DB::table('trading_orders as orders')
            ->join('trading_quotes as quotes', 'quotes.id', '=', 'orders.trading_quote_id')
            ->where('orders.scope_hash', hash('sha256', $scope->key()))
            ->where('quotes.quote_uuid', $quoteId->value())
            ->select('orders.*', 'quotes.quote_uuid')
            ->first();

        return $row === null ? null : $this->rehydrate($scope, $row);
    }

    private function rehydrate(FinancialScope $scope, object $row): Order
    {
        return Order::rehydrate(
            id: OrderId::fromString($row->order_uuid),
            quoteId: QuoteId::fromString($row->quote_uuid),
            scope: $scope,
            traceId: TraceId::fromString($row->trace_id),
            correlationId: CorrelationId::fromString($row->correlation_id),
            idempotencyKey: IdempotencyKey::fromString($row->idempotency_key),
            status: OrderStatus::from($row->status),
            createdAt: new DateTimeImmutable($row->created_at),
            submittedAt: $row->submitted_at === null ? null : new DateTimeImmutable($row->submitted_at),
            rejectionReason: $row->rejection_reason,
        );
    }
}
