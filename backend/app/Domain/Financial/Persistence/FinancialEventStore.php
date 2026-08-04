<?php

namespace App\Domain\Financial\Persistence;

use App\Domain\Financial\Events\FinancialEvent;
use App\Domain\Financial\ValueObjects\CorrelationId;

interface FinancialEventStore
{
    public function append(FinancialEvent $event): void;

    /** @return list<FinancialEvent> */
    public function byCorrelationId(CorrelationId $correlationId): array;
}
