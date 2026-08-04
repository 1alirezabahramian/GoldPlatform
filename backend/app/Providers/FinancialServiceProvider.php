<?php

namespace App\Providers;

use App\Domain\Financial\Contracts\TenantScopedBalanceProjectionRepository;
use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Contracts\TenantScopedJournalRepository;
use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use App\Domain\Financial\Persistence\ConcurrencyGuard;
use App\Infrastructure\Financial\Database\DatabaseTenantScopedBalanceProjectionRepository;
use App\Infrastructure\Financial\Database\DatabaseTenantScopedFinancialEventStore;
use App\Infrastructure\Financial\Database\DatabaseTenantScopedIdempotencyRegistry;
use App\Infrastructure\Financial\Database\DatabaseTenantScopedJournalRepository;
use App\Infrastructure\Financial\Laravel\LaravelCacheConcurrencyGuard;
use App\Infrastructure\Financial\Laravel\LaravelDatabaseAtomicFinancialOperation;
use Illuminate\Support\ServiceProvider;

final class FinancialServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AtomicFinancialOperation::class,
            LaravelDatabaseAtomicFinancialOperation::class,
        );

        $this->app->bind(
            ConcurrencyGuard::class,
            LaravelCacheConcurrencyGuard::class,
        );

        $this->app->bind(
            TenantScopedJournalRepository::class,
            DatabaseTenantScopedJournalRepository::class,
        );

        $this->app->bind(
            TenantScopedFinancialEventStore::class,
            DatabaseTenantScopedFinancialEventStore::class,
        );

        $this->app->bind(
            TenantScopedIdempotencyRegistry::class,
            DatabaseTenantScopedIdempotencyRegistry::class,
        );

        $this->app->bind(
            TenantScopedBalanceProjectionRepository::class,
            DatabaseTenantScopedBalanceProjectionRepository::class,
        );
    }
}
