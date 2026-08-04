<?php

namespace App\Infrastructure\Financial\InMemory;

use App\Domain\Financial\Events\FinancialEvent;
use App\Domain\Financial\Persistence\FinancialEventStore;
use App\Domain\Financial\ValueObjects\CorrelationId;

final class InMemoryFinancialEventStore implements FinancialEventStore
{
    /** @var list<FinancialEvent> */
    private array $events = [];

    public function append(FinancialEvent $event): void
    {
        $this->events[] = $event;
    }

    public function byCorrelationId(CorrelationId $correlationId): array
    {
        return array_values(array_filter(
            $this->events,
            static fn (FinancialEvent $event): bool => $event->correlationId()->equals($correlationId),
        ));
    }
}
