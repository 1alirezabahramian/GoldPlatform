<?php

namespace App\Domain\Trading\Validation;

use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Trading\Order\Order;
use App\Domain\Trading\Quote\Quote;

final readonly class OrderValidationContext
{
    public function __construct(
        private FinancialScope $scope,
        private Order $order,
        private Quote $quote,
    ) {}

    public function scope(): FinancialScope { return $this->scope; }
    public function order(): Order { return $this->order; }
    public function quote(): Quote { return $this->quote; }
}
