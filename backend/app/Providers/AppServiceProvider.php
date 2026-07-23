<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\Sms\Contracts\SmsProvider;
use App\Services\Sms\Providers\SmsIrProvider;
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(

            SmsProvider::class,

            SmsIrProvider::class

        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    User::observe(UserObserver::class);
}
}