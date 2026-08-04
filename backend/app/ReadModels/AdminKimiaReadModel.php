<?php

namespace App\ReadModels;

final class AdminKimiaReadModel
{
    /** @return array<string, mixed> */
    public function overview(): array
    {
        $baseUrl = (string) config('services.kimia.base_url');
        $username = (string) config('services.kimia.username');
        $password = (string) config('services.kimia.password');
        $timeout = (int) config('services.kimia.timeout', 30);
        $readOnly = (bool) config('services.kimia.read_only', true);

        return [
            'configuration' => [
                'configured' => $baseUrl !== '' && $username !== '' && $password !== '' && $timeout > 0,
                'base_url_configured' => $baseUrl !== '',
                'credentials_configured' => $username !== '' && $password !== '',
                'timeout_seconds' => $timeout,
                'read_retries' => max(0, (int) config('services.kimia.read_retries', 2)),
                'retry_delay_ms' => max(0, (int) config('services.kimia.retry_delay_ms', 250)),
            ],
            'mode' => [
                'read_enabled' => true,
                'write_enabled' => ! $readOnly,
                'read_only' => $readOnly,
            ],
            'observability' => [
                'active_probe_supported' => false,
                'last_sync_supported' => false,
                'latency_history_supported' => false,
                'request_log_store_supported' => false,
                'last_sync_at' => null,
                'latency_ms' => null,
                'connection_status' => 'not_probed',
            ],
        ];
    }
}
