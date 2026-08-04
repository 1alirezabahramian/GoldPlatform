<?php

namespace App\Infrastructure\Financial\InMemory;

use App\Domain\Financial\Contracts\TenantScopedJournalRepository;
use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\TraceId;

final class InMemoryTenantScopedJournalRepository implements TenantScopedJournalRepository
{
    /** @var array<string, JournalDocument> */
    private array $documents = [];

    public function save(FinancialScope $scope, JournalDocument $document): void
    {
        $this->documents[$this->key($scope, $document->journal()->traceId())] = $document;
    }

    public function findByTraceId(FinancialScope $scope, TraceId $traceId): ?JournalDocument
    {
        return $this->documents[$this->key($scope, $traceId)] ?? null;
    }

    private function key(FinancialScope $scope, TraceId $traceId): string
    {
        return $scope->key().'|trace|'.$traceId->value();
    }
}
