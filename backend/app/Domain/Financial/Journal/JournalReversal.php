<?php

namespace App\Domain\Financial\Journal;

final readonly class JournalReversal
{
    public function __construct(
        private JournalDocument $original,
        private JournalDocument $reversal,
    ) {
    }

    public function original(): JournalDocument
    {
        return $this->original;
    }

    public function reversal(): JournalDocument
    {
        return $this->reversal;
    }
}
