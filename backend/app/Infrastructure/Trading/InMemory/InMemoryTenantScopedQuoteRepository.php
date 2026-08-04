<?php

namespace App\Infrastructure\Trading\InMemory;

use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Trading\Contracts\TenantScopedQuoteRepository;
use App\Domain\Trading\Quote\Quote;
use App\Domain\Trading\ValueObjects\QuoteId;

final class InMemoryTenantScopedQuoteRepository implements TenantScopedQuoteRepository
{
    /** @var array<string, Quote> */
    private array $quotes = [];

    public function save(FinancialScope $scope, Quote $quote): void
    {
        if ($scope->key() !== $quote->scope()->key()) {
            throw new \DomainException('Quote scope does not match repository scope.');
        }

        $this->quotes[$this->key($scope, $quote->id())] = $quote;
    }

    public function find(FinancialScope $scope, QuoteId $quoteId): ?Quote
    {
        return $this->quotes[$this->key($scope, $quoteId)] ?? null;
    }

    private function key(FinancialScope $scope, QuoteId $quoteId): string
    {
        return hash('sha256', $scope->key()).':'.$quoteId->value();
    }
}
