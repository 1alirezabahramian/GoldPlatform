<?php

namespace App\Infrastructure\Financial\InMemory;

use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\Events\FinancialEvent;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;

final class InMemoryTenantScopedFinancialEventStore implements TenantScopedFinancialEventStore
{
    /** @var array<string, list<FinancialEvent>> */
    private array $events = [];

    public function append(FinancialScope $scope, FinancialEvent $event): void
    {
        $key = $this->key($scope, $event->correlationId());
        $this->events[$key] ??= [];
        $this->events[$key][] = $event;
    }

    public function byCorrelationId(FinancialScope $scope, CorrelationId $correlationId): array
    {
        return $this->events[$this->key($scope, $correlationId)] ?? [];
    }

    private function key(FinancialScope $scope, CorrelationId $correlationId): string
    {
        return $scope->key().'|correlation|'.$correlationId->value();
    }
}
