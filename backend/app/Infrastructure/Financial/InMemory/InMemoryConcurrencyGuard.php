<?php

namespace App\Infrastructure\Financial\InMemory;

use App\Domain\Financial\Persistence\ConcurrencyGuard;

final class InMemoryConcurrencyGuard implements ConcurrencyGuard
{
    /** @var array<string, bool> */
    private array $locks = [];

    public function synchronized(string $resource, callable $operation): mixed
    {
        if (($this->locks[$resource] ?? false) === true) {
            throw new \RuntimeException('Concurrent financial operation detected.');
        }

        $this->locks[$resource] = true;

        try {
            return $operation();
        } finally {
            unset($this->locks[$resource]);
        }
    }
}
