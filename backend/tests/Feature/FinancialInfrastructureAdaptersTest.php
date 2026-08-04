<?php

namespace Tests\Feature;

use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use App\Domain\Financial\Persistence\ConcurrencyGuard;
use App\Infrastructure\Financial\Laravel\LaravelCacheConcurrencyGuard;
use App\Infrastructure\Financial\Laravel\LaravelDatabaseAtomicFinancialOperation;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class FinancialInfrastructureAdaptersTest extends TestCase
{
    public function test_financial_infrastructure_contracts_resolve_to_laravel_adapters(): void
    {
        self::assertInstanceOf(
            LaravelDatabaseAtomicFinancialOperation::class,
            app(AtomicFinancialOperation::class),
        );

        self::assertInstanceOf(
            LaravelCacheConcurrencyGuard::class,
            app(ConcurrencyGuard::class),
        );
    }

    public function test_database_atomic_operation_rolls_back_all_changes_on_failure(): void
    {
        DB::statement('CREATE TABLE financial_atomic_test (id INTEGER PRIMARY KEY, value VARCHAR(50))');

        try {
            app(AtomicFinancialOperation::class)->execute(function (): void {
                DB::table('financial_atomic_test')->insert(['id' => 1, 'value' => 'pending']);

                throw new RuntimeException('force rollback');
            });

            self::fail('The transaction should have thrown.');
        } catch (RuntimeException $exception) {
            self::assertSame('force rollback', $exception->getMessage());
        }

        self::assertSame(0, DB::table('financial_atomic_test')->count());
    }

    public function test_concurrency_guard_executes_the_operation_under_a_named_lock(): void
    {
        $result = app(ConcurrencyGuard::class)->synchronized(
            'posting:test-key',
            static fn (): string => 'locked-result',
        );

        self::assertSame('locked-result', $result);
    }

    public function test_concurrency_guard_rejects_an_empty_resource_name(): void
    {
        $this->expectException(RuntimeException::class);

        app(ConcurrencyGuard::class)->synchronized('  ', static fn (): null => null);
    }
}
