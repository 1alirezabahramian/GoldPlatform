<?php

namespace App\Providers;

use App\Domain\Financial\Persistence\AtomicFinancialOperation;
use App\Domain\Financial\Persistence\ConcurrencyGuard;
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
    }
}
