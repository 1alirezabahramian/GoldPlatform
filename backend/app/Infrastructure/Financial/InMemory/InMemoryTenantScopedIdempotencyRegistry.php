<?php

namespace App\Infrastructure\Financial\InMemory;

use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use RuntimeException;

final class InMemoryTenantScopedIdempotencyRegistry implements TenantScopedIdempotencyRegistry
{
    /** @var array<string, IdempotencyRecord> */
    private array $records = [];

    public function find(FinancialScope $scope, IdempotencyKey $key): ?IdempotencyRecord
    {
        return $this->records[$this->key($scope, $key)] ?? null;
    }

    public function claim(FinancialScope $scope, IdempotencyRecord $record): void
    {
        $storageKey = $this->key($scope, $record->key());
        $existing = $this->records[$storageKey] ?? null;

        if ($existing !== null && ! $existing->matches($record->operation(), $record->requestHash())) {
            throw new RuntimeException('Idempotency key was already used with a different request in this financial scope.');
        }

        $this->records[$storageKey] = $existing ?? $record;
    }

    private function key(FinancialScope $scope, IdempotencyKey $key): string
    {
        return $scope->key().'|'.$key->value();
    }
}
