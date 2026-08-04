<?php

namespace App\Domain\Financial\Journal;

use App\Domain\Financial\Enums\JournalStatus;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use DomainException;

final readonly class JournalDocument
{
    private function __construct(
        private Journal $journal,
        private JournalStatus $status,
        private ?TraceId $reversalTraceId = null,
    ) {
    }

    public static function draft(Journal $journal): self
    {
        return new self($journal, JournalStatus::DRAFT);
    }

    public static function rehydrate(
        Journal $journal,
        JournalStatus $status,
        ?TraceId $reversalTraceId = null,
    ): self {
        if ($status === JournalStatus::REVERSED && $reversalTraceId === null) {
            throw new DomainException('A reversed journal requires its reversal trace identifier.');
        }

        if ($status !== JournalStatus::REVERSED && $reversalTraceId !== null) {
            throw new DomainException('Only a reversed journal may contain a reversal trace identifier.');
        }

        return new self($journal, $status, $reversalTraceId);
    }

    public function journal(): Journal
    {
        return $this->journal;
    }

    public function status(): JournalStatus
    {
        return $this->status;
    }

    public function reversalTraceId(): ?TraceId
    {
        return $this->reversalTraceId;
    }

    public function post(): self
    {
        if ($this->status !== JournalStatus::DRAFT) {
            throw new DomainException('Only a draft journal can be posted.');
        }

        return new self($this->journal, JournalStatus::POSTED);
    }

    public function reverse(
        TraceId $traceId,
        IdempotencyKey $idempotencyKey,
    ): JournalReversal {
        if ($this->status !== JournalStatus::POSTED) {
            throw new DomainException('Only a posted journal can be reversed.');
        }

        $reversal = $this->journal->reversed($traceId, $idempotencyKey);

        return new JournalReversal(
            original: new self(
                journal: $this->journal,
                status: JournalStatus::REVERSED,
                reversalTraceId: $traceId,
            ),
            reversal: new self(
                journal: $reversal,
                status: JournalStatus::POSTED,
            ),
        );
    }
}
