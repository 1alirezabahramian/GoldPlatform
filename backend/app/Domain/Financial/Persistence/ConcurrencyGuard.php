<?php

namespace App\Domain\Financial\Persistence;

interface ConcurrencyGuard
{
    /** @template T */
    public function synchronized(string $resource, callable $operation): mixed;
}
