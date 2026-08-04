<?php

namespace App\Infrastructure\Financial\InMemory;

use App\Domain\Financial\Contracts\IdempotencyRegistry;
use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use RuntimeException;

final class InMemoryIdempotencyRegistry implements IdempotencyRegistry
{
    /** @var array<string, IdempotencyRecord> */
    private array $records = [];

    public function find(IdempotencyKey $key): ?IdempotencyRecord
    {
        return $this->records[$key->value()] ?? null;
    }

    public function claim(IdempotencyRecord $record): void
    {
        $key = $record->key()->value();
        $existing = $this->records[$key] ?? null;

        if ($existing !== null && ! $existing->matches($record->operation(), $record->requestHash())) {
            throw new RuntimeException('Idempotency key was already used with a different request.');
        }

        $this->records[$key] = $existing ?? $record;
    }
}
