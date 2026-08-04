<?php

namespace App\Support;

use App\Models\OutboxMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class OperationalHealthService
{
    /**
     * @return array<string, mixed>
     */
    public function snapshot(): array
    {
        $components = [
            'database' => $this->probe(fn () => DB::select('select 1')),
            'redis' => $this->probe(fn () => Redis::connection()->ping()),
            'storage' => $this->storageProbe(),
            'queue' => $this->queueProbe(),
            'outbox' => $this->outboxProbe(),
            'kimia_safety' => [
                'status' => ((bool) config('services.kimia.read_only', true) && ! (bool) config('kimia_write.enabled', false))
                    ? 'ok'
                    : 'degraded',
                'read_only' => (bool) config('services.kimia.read_only', true),
                'write_enabled' => (bool) config('kimia_write.enabled', false),
            ],
        ];

        $overall = collect($components)->contains(fn (array $component): bool => $component['status'] !== 'ok')
            ? 'degraded'
            : 'ok';

        return [
            'status' => $overall,
            'checked_at' => now()->toIso8601String(),
            'components' => $components,
        ];
    }

    /**
     * @param callable(): mixed $callback
     * @return array<string, mixed>
     */
    private function probe(callable $callback): array
    {
        $started = microtime(true);

        try {
            $callback();

            return [
                'status' => 'ok',
                'latency_ms' => round((microtime(true) - $started) * 1000, 2),
            ];
        } catch (Throwable $exception) {
            return [
                'status' => 'degraded',
                'latency_ms' => round((microtime(true) - $started) * 1000, 2),
                'error_class' => $exception::class,
            ];
        }
    }

    /** @return array<string, mixed> */
    private function storageProbe(): array
    {
        return [
            'status' => is_writable(storage_path()) ? 'ok' : 'degraded',
            'writable' => is_writable(storage_path()),
        ];
    }

    /** @return array<string, mixed> */
    private function queueProbe(): array
    {
        try {
            $failed = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;

            return [
                'status' => $failed === 0 ? 'ok' : 'degraded',
                'failed_jobs' => $failed,
            ];
        } catch (Throwable $exception) {
            return ['status' => 'degraded', 'error_class' => $exception::class];
        }
    }

    /** @return array<string, mixed> */
    private function outboxProbe(): array
    {
        try {
            $pending = Schema::hasTable('outbox_messages')
                ? OutboxMessage::query()
                    ->whereNull('processed_at')
                    ->where(fn ($query) => $query->whereNull('available_at')->orWhere('available_at', '<=', now()))
                    ->count()
                : 0;

            return [
                'status' => 'ok',
                'pending_messages' => $pending,
            ];
        } catch (Throwable $exception) {
            return ['status' => 'degraded', 'error_class' => $exception::class];
        }
    }
}
