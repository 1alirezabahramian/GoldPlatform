<?php

namespace App\Infrastructure\Financial\InMemory;

use App\Domain\Financial\Persistence\AtomicFinancialOperation;

final class InMemoryAtomicFinancialOperation implements AtomicFinancialOperation
{
    public function execute(callable $operation): mixed
    {
        return $operation();
    }
}
