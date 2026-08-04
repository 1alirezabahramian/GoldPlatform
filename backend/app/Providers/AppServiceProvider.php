<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\Services\Sms\Contracts\SmsProvider;
use App\Services\Sms\Providers\SmsIrProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SmsProvider::class, SmsIrProvider::class);
    }

    public function boot(): void
    {
        User::observe(UserObserver::class);
        $this->configureRateLimits();

        DB::listen(function (QueryExecuted $query): void {
            $threshold = (int) config('operations.slow_query_ms', 500);

            if ($query->time < $threshold) {
                return;
            }

            Log::warning('Slow database query detected.', [
                'connection' => $query->connectionName,
                'duration_ms' => $query->time,
                'sql_template' => $query->sql,
            ]);
        });
    }

    private function configureRateLimits(): void
    {
        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute((int) env('RATE_LIMIT_AUTH_PER_MINUTE', 10))
            ->by('auth:'.$request->ip()));

        RateLimiter::for('customer', fn (Request $request) => Limit::perMinute((int) env('RATE_LIMIT_CUSTOMER_PER_MINUTE', 120))
            ->by('customer:'.($request->user()?->getAuthIdentifier() ?? $request->ip())));

        RateLimiter::for('operator', fn (Request $request) => Limit::perMinute((int) env('RATE_LIMIT_OPERATOR_PER_MINUTE', 240))
            ->by('operator:'.($request->user()?->getAuthIdentifier() ?? $request->ip())));

        RateLimiter::for('admin', fn (Request $request) => Limit::perMinute((int) env('RATE_LIMIT_ADMIN_PER_MINUTE', 180))
            ->by('admin:'.($request->user()?->getAuthIdentifier() ?? $request->ip())));

        RateLimiter::for('public-read', fn (Request $request) => Limit::perMinute((int) env('RATE_LIMIT_PUBLIC_READ_PER_MINUTE', 60))
            ->by('public-read:'.$request->ip()));
    }
}
