<?php

namespace Tests\Feature;

use App\Infrastructure\Financial\Laravel\LaravelCacheConcurrencyGuard;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Tests\TestCase;

final class FinancialConcurrencyGuardTest extends TestCase
{
    public function test_held_financial_lock_rejects_a_competing_operation(): void
    {
        $resource = 'tenant:tenant-a:company:*:branch:*:idempotency:request-1';
        $heldLock = Cache::lock('financial:'.$resource, 10);

        self::assertTrue($heldLock->get());

        try {
            $guard = new LaravelCacheConcurrencyGuard(lockSeconds: 10, waitSeconds: 0);

            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('Financial operation could not acquire its concurrency lock.');

            $guard->synchronized($resource, static fn (): string => 'must-not-run');
        } finally {
            $heldLock->release();
        }
    }

    public function test_released_financial_lock_allows_the_next_operation(): void
    {
        $resource = 'tenant:tenant-a:company:*:branch:*:idempotency:request-2';
        $heldLock = Cache::lock('financial:'.$resource, 10);

        self::assertTrue($heldLock->get());
        $heldLock->release();

        $guard = new LaravelCacheConcurrencyGuard(lockSeconds: 10, waitSeconds: 0);

        self::assertSame(
            'executed',
            $guard->synchronized($resource, static fn (): string => 'executed'),
        );
    }

    public function test_empty_lock_resource_is_rejected(): void
    {
        $guard = new LaravelCacheConcurrencyGuard();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Concurrency resource cannot be empty.');

        $guard->synchronized('   ', static fn (): null => null);
    }
}
