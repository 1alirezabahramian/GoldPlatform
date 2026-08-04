<?php

namespace App\Providers;

use App\Domain\Financial\Contracts\TenantScopedBalanceProjectionRepository;
use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Contracts\TenantScopedJournalRepository;
use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use App\Domain\Financial\Persistence\ConcurrencyGuard;
use App\Infrastructure\Financial\Database\DatabaseTenantScopedFinancialEventStore;
use App\Infrastructure\Financial\Database\DatabaseTenantScopedIdempotencyRegistry;
use App\Infrastructure\Financial\InMemory\InMemoryTenantScopedBalanceProjectionRepository;
use App\Infrastructure\Financial\InMemory\InMemoryTenantScopedJournalRepository;
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

        $this->app->singleton(
            TenantScopedJournalRepository::class,
            InMemoryTenantScopedJournalRepository::class,
        );

        $this->app->bind(
            TenantScopedFinancialEventStore::class,
            DatabaseTenantScopedFinancialEventStore::class,
        );

        $this->app->bind(
            TenantScopedIdempotencyRegistry::class,
            DatabaseTenantScopedIdempotencyRegistry::class,
        );

        $this->app->singleton(
            TenantScopedBalanceProjectionRepository::class,
            InMemoryTenantScopedBalanceProjectionRepository::class,
        );
    }
}
