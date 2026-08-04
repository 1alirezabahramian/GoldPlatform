<?php

namespace App\Domain\Financial\Contracts;

use App\Domain\Financial\Events\FinancialEvent;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;

interface TenantScopedFinancialEventStore
{
    public function append(FinancialScope $scope, FinancialEvent $event): void;

    /** @return list<FinancialEvent> */
    public function byCorrelationId(FinancialScope $scope, CorrelationId $correlationId): array;
}
