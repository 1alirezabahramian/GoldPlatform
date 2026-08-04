<?php

namespace App\Domain\Financial\Persistence;

use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\ValueObjects\TraceId;

/**
 * @deprecated Use \App\Domain\Financial\Contracts\TenantScopedJournalRepository.
 * New financial flows must provide FinancialScope.
 */
interface JournalRepository
{
    public function save(JournalDocument $document): void;

    public function findByTraceId(TraceId $traceId): ?JournalDocument;
}
