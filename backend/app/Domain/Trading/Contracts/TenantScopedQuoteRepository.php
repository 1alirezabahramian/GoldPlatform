<?php

namespace App\Domain\Trading\Contracts;

use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Trading\Quote\Quote;
use App\Domain\Trading\ValueObjects\QuoteId;

interface TenantScopedQuoteRepository
{
    public function save(FinancialScope $scope, Quote $quote): void;

    public function find(FinancialScope $scope, QuoteId $quoteId): ?Quote;
}
