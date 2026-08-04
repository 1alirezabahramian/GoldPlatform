<?php

namespace App\Providers;

use App\Domain\Trading\Contracts\TenantScopedOrderRepository;
use App\Domain\Trading\Contracts\TenantScopedQuoteRepository;
use App\Infrastructure\Trading\Database\DatabaseTenantScopedOrderRepository;
use App\Infrastructure\Trading\Database\DatabaseTenantScopedQuoteRepository;
use Illuminate\Support\ServiceProvider;

final class TradingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TenantScopedQuoteRepository::class,
            DatabaseTenantScopedQuoteRepository::class,
        );

        $this->app->bind(
            TenantScopedOrderRepository::class,
            DatabaseTenantScopedOrderRepository::class,
        );
    }
}
