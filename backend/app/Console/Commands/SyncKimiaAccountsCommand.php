<?php

namespace App\Console\Commands;

use App\Enums\AccountType;
use App\Integrations\Kimia\Adapters\AccountAdapter;
use App\Integrations\Kimia\DTO\AccountDTO;
use App\Integrations\Kimia\Repositories\KimiaAccountRepository;
use App\Models\ExternalAccount;
use Illuminate\Console\Command;
use Throwable;

class SyncKimiaAccountsCommand extends Command
{
    protected $signature = 'kimia:sync-accounts
                            {--type=* : Kimia account types to synchronize}';

    protected $description = 'Synchronize Kimia accounts with the local external_accounts table';

    public function handle(
        KimiaAccountRepository $accountsRepository,
        AccountAdapter $adapter,
    ): int {
        $types = $this->requestedTypes();
        $accounts = [];

        try {
            foreach ($types as $type) {
                foreach ($accountsRepository->all($type) as $account) {
                    $accounts[(string) $account->id] = $account;
                }
            }
        } catch (Throwable $exception) {
            report($exception);

            $this->error(
                'Kimia account synchronization failed: '
                .$exception->getMessage()
            );

            return self::FAILURE;
        }

        if ($accounts === []) {
            $this->error('No accounts received from Kimia.');

            return self::FAILURE;
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        /** @var AccountDTO $account */
        foreach ($accounts as $account) {
            $data = $adapter->toArray($account);
            $syncHash = $this->makeSyncHash($account->rawData);

            $model = ExternalAccount::query()
                ->where('provider', 'kimia')
                ->where('external_id', $account->id)
                ->first();

            if (
                $model !== null
                && is_string($model->sync_hash)
                && hash_equals($model->sync_hash, $syncHash)
            ) {
                $skipped++;

                continue;
            }

            $persisted = [
                ...$data,
                'sync_hash' => $syncHash,
                'sync_status' => 'synced',
                'sync_error' => null,
                'last_synced_at' => now(),
            ];

            if ($model === null) {
                ExternalAccount::create([
                    'provider' => 'kimia',
                    ...$persisted,
                ]);

                $created++;

                continue;
            }

            $model->update($persisted);
            $updated++;
        }

        $this->table(
            ['Received', 'Created', 'Updated', 'Skipped'],
            [[count($accounts), $created, $updated, $skipped]]
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

        $validTypes = array_map(
            fn (AccountType $type): int => $type->value,
            AccountType::cases()
        );

        return array_values(
            array_unique(
                array_intersect(
                    array_map('intval', $requested),
                    $validTypes
                )
            )
        );
    }

    /**
     * @param array<string, mixed> $account
     */
    private function makeSyncHash(array $account): string
    {
        $json = json_encode(
            $account,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_THROW_ON_ERROR
        );

        return hash('sha256', $json);
    }
}
