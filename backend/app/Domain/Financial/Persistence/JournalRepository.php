<?php

namespace App\Domain\Financial\Persistence;

use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\ValueObjects\TraceId;

interface JournalRepository
{
    public function save(JournalDocument $document): void;

    public function findByTraceId(TraceId $traceId): ?JournalDocument;
}
