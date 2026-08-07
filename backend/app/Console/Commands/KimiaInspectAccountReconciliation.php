<?php

namespace App\Console\Commands;

use App\Services\Kimia\CustomerAccountReconciliationService;
use Illuminate\Console\Command;

final class KimiaInspectAccountReconciliation extends Command
{
    protected $signature = 'kimia:inspect-account-reconciliation {--json : Output machine-readable JSON}';

    protected $description = 'Inspect customer-to-Kimia account reconciliation without mutating data';

    public function handle(CustomerAccountReconciliationService $service): int
    {
        $result = $service->inspect();

        if ($this->option('json')) {
            $this->line((string) json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        $summaryRows = [];
        foreach ($result['summary'] as $name => $count) {
            $summaryRows[] = [$name, $count];
        }

        $this->table(['Metric', 'Count'], $summaryRows);

        if ($result['rows'] === []) {
            $this->info('No account reconciliation rows found.');

            return self::SUCCESS;
        }

        $this->table(
            ['Status', 'Local Account', 'Kimia AccountId', 'External Account', 'User Count'],
            array_map(
                static fn (array $row): array => [
                    $row['status'],
                    $row['local_account_id'],
                    $row['kimia_account_id'],
                    $row['external_account_id'],
                    $row['user_count'],
                ],
                $result['rows']
            )
        );

        $hasConflict = $result['summary']['duplicate_user_binding'] > 0
            || $result['summary']['orphaned_user_bindings'] > 0
            || $result['summary']['account_missing_kimia_id'] > 0;

        if ($hasConflict) {
            $this->warn('Reconciliation conflicts detected. No data was changed.');
        } else {
            $this->info('Reconciliation inspection completed. No data was changed.');
        }

        return self::SUCCESS;
    }
}
