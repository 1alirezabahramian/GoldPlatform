<?php

namespace App\Domain\Financial\Contracts;

use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;

interface TenantScopedIdempotencyRegistry
{
    public function find(FinancialScope $scope, IdempotencyKey $key): ?IdempotencyRecord;

    public function claim(FinancialScope $scope, IdempotencyRecord $record): void;
}
