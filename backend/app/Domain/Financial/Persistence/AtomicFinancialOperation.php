<?php

namespace App\Domain\Financial\Persistence;

interface AtomicFinancialOperation
{
    /** @template T */
    public function execute(callable $operation): mixed;
}
