<?php

namespace App\Infrastructure\Financial\InMemory;

use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\Persistence\JournalRepository;
use App\Domain\Financial\ValueObjects\TraceId;

final class InMemoryJournalRepository implements JournalRepository
{
    /** @var array<string, JournalDocument> */
    private array $documents = [];

    public function save(JournalDocument $document): void
    {
        $this->documents[$document->journal()->traceId()->value()] = $document;
    }

    public function findByTraceId(TraceId $traceId): ?JournalDocument
    {
        return $this->documents[$traceId->value()] ?? null;
    }
}
