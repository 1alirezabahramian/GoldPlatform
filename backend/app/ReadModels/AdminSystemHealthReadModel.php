<?php

namespace App\ReadModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class AdminSystemHealthReadModel
{
    /** @return array<string, mixed> */
    public function read(): array
    {
        $database = $this->probeDatabase();
        $redis = $this->probeRedis();

        return [
            'overall_status' => ($database['status'] === 'up' && ! in_array($redis['status'], ['down'], true)) ? 'operational' : 'degraded',
            'components' => [
                'database' => $database,
                'redis' => $redis,
                'cache' => [
                    'driver' => (string) config('cache.default'),
                    'status' => config('cache.default') === 'redis' ? $redis['status'] : 'configured',
                ],
                'queue' => [
                    'connection' => (string) config('queue.default'),
                    'pending_jobs' => $this->safeCount('jobs'),
                    'failed_jobs' => $this->safeCount('failed_jobs'),
                ],
                'outbox' => [
                    'total' => $this->safeCount('outbox_messages'),
                    'pending' => $this->safePendingOutboxCount(),
                ],
                'storage' => [
                    'status' => is_dir(storage_path()) && is_writable(storage_path()) ? 'writable' : 'unavailable',
                ],
                'docker' => [
                    'supported' => false,
                    'status' => 'not_observable_from_application',
                ],
            ],
            'runtime' => [
                'environment' => app()->environment(),
                'debug' => (bool) config('app.debug'),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
            ],
        ];
    }

    /** @return array{status:string} */
    private function probeDatabase(): array
    {
        try {
            DB::select('SELECT 1');
            return ['status' => 'up'];
        } catch (Throwable) {
            return ['status' => 'down'];
        }
    }

    /** @return array{status:string} */
    private function probeRedis(): array
    {
        $usesRedis = in_array(config('cache.default'), ['redis'], true)
            || in_array(config('queue.default'), ['redis'], true)
            || config('session.driver') === 'redis';

        if (! $usesRedis) {
            return ['status' => 'not_in_use'];
        }

        try {
            Redis::connection()->ping();
            return ['status' => 'up'];
        } catch (Throwable) {
            return ['status' => 'down'];
        }
    }

    private function safeCount(string $table): ?int
    {
        try {
            return Schema::hasTable($table) ? DB::table($table)->count() : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function safePendingOutboxCount(): ?int
    {
        try {
            if (! Schema::hasTable('outbox_messages')) {
                return null;
            }

            return Schema::hasColumn('outbox_messages', 'processed_at')
                ? DB::table('outbox_messages')->whereNull('processed_at')->count()
                : null;
        } catch (Throwable) {
            return null;
        }
    }
}
