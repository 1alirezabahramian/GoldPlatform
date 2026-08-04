<?php

namespace App\Console\Commands;

use App\Integrations\Kimia\Services\KimiaReadValidatorService;
use Illuminate\Console\Command;

final class KimiaValidateRead extends Command
{
    protected $signature = 'kimia:validate-read {--account-id= : Optional real Kimia account ID for balance and transaction checks} {--json : Output machine-readable JSON}';

    protected $description = 'Validate confirmed Kimia read-only endpoints without exposing credentials or payload data.';

    public function handle(KimiaReadValidatorService $validator): int
    {
        if (! (bool) config('services.kimia.read_only', true)) {
            $this->error('KIMIA_READ_ONLY must be enabled for this validation command.');

            return self::FAILURE;
        }

        $accountId = $this->option('account-id');
        $result = $validator->validate(
            is_numeric($accountId) ? (int) $accountId : null
        );

        if ($this->option('json')) {
            $this->line((string) json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $rows = collect($result['endpoints'])
                ->map(fn (array $endpoint, string $name): array => [
                    $name,
                    $endpoint['status'],
                    $endpoint['shape'] ?? '-',
                    $endpoint['items'] ?? '-',
                    $endpoint['duration_ms'],
                    $endpoint['error_type'] ?? '-',
                ])->values()->all();

            $this->table(
                ['Endpoint', 'Status', 'Shape', 'Items', 'Duration ms', 'Error'],
                $rows
            );
        }

        return $result['ok'] ? self::SUCCESS : self::FAILURE;
    }
}
