<?php

namespace App\Domain\Financial\Contracts;

use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\TraceId;

interface TenantScopedJournalRepository
{
    public function save(FinancialScope $scope, JournalDocument $document): void;

    public function findByTraceId(FinancialScope $scope, TraceId $traceId): ?JournalDocument;
}
