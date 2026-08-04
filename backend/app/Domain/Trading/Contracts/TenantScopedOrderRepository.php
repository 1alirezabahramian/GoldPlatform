<?php

namespace App\Domain\Trading\Contracts;

use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Trading\Order\Order;
use App\Domain\Trading\ValueObjects\OrderId;
use App\Domain\Trading\ValueObjects\QuoteId;

interface TenantScopedOrderRepository
{
    public function save(FinancialScope $scope, Order $order): void;

    public function find(FinancialScope $scope, OrderId $orderId): ?Order;

    public function findByQuote(FinancialScope $scope, QuoteId $quoteId): ?Order;
}
