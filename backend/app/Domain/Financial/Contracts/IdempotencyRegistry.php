<?php

namespace App\Domain\Financial\Contracts;

use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\ValueObjects\IdempotencyKey;

/**
 * @deprecated Use TenantScopedIdempotencyRegistry. New financial flows must provide FinancialScope.
 */
interface IdempotencyRegistry
{
    public function find(IdempotencyKey $key): ?IdempotencyRecord;

    public function claim(IdempotencyRecord $record): void;
}
