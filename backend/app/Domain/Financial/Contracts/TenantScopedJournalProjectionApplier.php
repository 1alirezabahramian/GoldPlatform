<?php

namespace App\Domain\Financial\Contracts;

use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\ValueObjects\FinancialScope;

interface TenantScopedJournalProjectionApplier
{
    public function apply(FinancialScope $scope, JournalDocument $document): void;
}
