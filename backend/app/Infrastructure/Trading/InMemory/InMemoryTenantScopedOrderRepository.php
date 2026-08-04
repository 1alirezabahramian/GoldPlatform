<?php

namespace App\Infrastructure\Trading\InMemory;

use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Trading\Contracts\TenantScopedOrderRepository;
use App\Domain\Trading\Order\Order;
use App\Domain\Trading\ValueObjects\OrderId;
use App\Domain\Trading\ValueObjects\QuoteId;
use DomainException;

final class InMemoryTenantScopedOrderRepository implements TenantScopedOrderRepository
{
    /** @var array<string, Order> */
    private array $orders = [];

    public function save(FinancialScope $scope, Order $order): void
    {
        if ($scope->key() !== $order->scope()->key()) {
            throw new DomainException('Order scope does not match repository scope.');
        }

        $this->orders[$this->key($scope, $order->id()->value())] = $order;
    }

    public function find(FinancialScope $scope, OrderId $orderId): ?Order
    {
        return $this->orders[$this->key($scope, $orderId->value())] ?? null;
    }

    public function findByQuote(FinancialScope $scope, QuoteId $quoteId): ?Order
    {
        foreach ($this->orders as $order) {
            if ($order->scope()->key() === $scope->key() && $order->quoteId()->equals($quoteId)) {
                return $order;
            }
        }

        return null;
    }

    private function key(FinancialScope $scope, string $id): string
    {
        return hash('sha256', $scope->key()).':'.$id;
    }
}
