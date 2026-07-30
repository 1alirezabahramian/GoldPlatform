<?php

namespace App\Console\Commands;

use App\Enums\AccountType;
use App\Models\ExternalAccount;
use App\Services\KimiaService;
use Illuminate\Console\Command;
use Throwable;

class SyncKimiaAccountsCommand extends Command
{
    protected $signature = 'kimia:sync-accounts
                            {--type=* : Kimia account types to synchronize}';

    protected $description = 'Synchronize Kimia accounts with the local external_accounts table';

    public function handle(KimiaService $kimia): int
    {
        $types = $this->requestedTypes();
        $accounts = [];

        try {
            foreach ($types as $type) {
                $response = $kimia->get('/api/account', ['Type' => $type]);
                $accounts = array_merge($accounts, $this->accountRows($response));
            }
        } catch (Throwable $exception) {
            $this->error('Kimia account synchronization failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        $accounts = collect($accounts)
            ->filter(fn (mixed $account): bool => is_array($account) && isset($account['AccountId']))
            ->keyBy('AccountId')
            ->values();

        if ($accounts->isEmpty()) {
            $this->error('No accounts received from Kimia.');

            return self::FAILURE;
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($accounts as $account) {
            if (! isset($account['Name'], $account['Type'])) {
                $skipped++;

                continue;
            }

            $data = [
                'external_id' => (int) $account['AccountId'],
                'code' => isset($account['AccountCode']) ? (string) $account['AccountCode'] : null,
                'name' => (string) $account['Name'],
                'type' => (int) $account['Type'],
                'mobile' => $account['Mobile'] ?? null,
                'national_id' => $account['NationalCode'] ?? null,
                'is_active' => (bool) ($account['IsVisible'] ?? true),
            ];

            $model = ExternalAccount::updateOrCreate(
                [
                    'provider' => 'kimia',
                    'external_id' => $data['external_id'],
                ],
                [
                    ...$data,
                    'raw_data' => $account,
                    'sync_hash' => hash('sha256', json_encode($account, JSON_UNESCAPED_UNICODE)),
                    'sync_status' => 'synced',
                    'sync_error' => null,
                    'last_synced_at' => now(),
                ]
            );

            $model->wasRecentlyCreated ? $created++ : $updated++;
        }

        $this->table(
            ['Received', 'Created', 'Updated', 'Skipped'],
            [[$accounts->count(), $created, $updated, $skipped]]
        );

        $this->info('Kimia account synchronization finished.');

        return self::SUCCESS;
    }

    /**
     * @return list<int>
     */
    private function requestedTypes(): array
    {
        $requested = $this->option('type');

        if ($requested === []) {
            return array_map(
                fn (AccountType $type): int => $type->value,
                AccountType::cases()
            );
        }

        return array_values(array_unique(array_map('intval', $requested)));
    }

    private function accountRows(array $response): array
    {
        foreach (['data', 'items', 'result'] as $key) {
            if (isset($response[$key]) && is_array($response[$key])) {
                return $response[$key];
            }
        }

        return array_is_list($response) ? $response : [];
    }
}
