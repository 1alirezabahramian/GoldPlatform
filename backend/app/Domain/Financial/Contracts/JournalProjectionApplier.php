<?php

namespace App\Domain\Financial\Contracts;

use App\Domain\Financial\Journal\JournalDocument;

interface JournalProjectionApplier
{
    public function apply(JournalDocument $postedJournal): void;
}
