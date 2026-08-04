<?php

namespace App\Infrastructure\Financial\Laravel;

use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use Illuminate\Support\Facades\DB;

final class LaravelDatabaseAtomicFinancialOperation implements AtomicFinancialOperation
{
    public function execute(callable $operation): mixed
    {
        return DB::transaction($operation);
    }
}
