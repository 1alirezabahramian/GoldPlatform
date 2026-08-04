<?php

namespace App\Infrastructure\Financial\Laravel;

use App\Domain\Financial\Persistence\ConcurrencyGuard;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

final class LaravelCacheConcurrencyGuard implements ConcurrencyGuard
{
    public function __construct(
        private readonly int $lockSeconds = 10,
        private readonly int $waitSeconds = 5,
    ) {}

    public function synchronized(string $resource, callable $operation): mixed
    {
        if (trim($resource) === '') {
            throw new RuntimeException('Concurrency resource cannot be empty.');
        }

        try {
            return Cache::lock('financial:'.$resource, $this->lockSeconds)
                ->block($this->waitSeconds, $operation);
        } catch (LockTimeoutException $exception) {
            throw new RuntimeException(
                'Financial operation could not acquire its concurrency lock.',
                previous: $exception,
            );
        }
    }
}
