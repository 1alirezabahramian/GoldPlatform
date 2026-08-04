<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\Services\Sms\Contracts\SmsProvider;
use App\Services\Sms\Providers\SmsIrProvider;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
}
