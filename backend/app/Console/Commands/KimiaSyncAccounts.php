<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Repositories\Kimia\AccountRepository;
use Illuminate\Console\Command;

class KimiaSyncAccounts extends Command
{
    protected $signature = 'kimia:sync-accounts';

    protected $description = 'Synchronize all Kimia accounts';

    public function handle(AccountRepository $repository): int
    {
        $this->info('Connecting to Kimia...');

        $accounts = $repository->all();

        if (empty($accounts)) {

            $this->error('No accounts received from Kimia.');

            return self::FAILURE;
        }

        $created = 0;
        $updated = 0;

        foreach ($accounts as $item) {

            $model = Account::updateOrCreate(

                [
                    'kimia_id' => $item['AccountId'],
                ],

                [
                    'account_code' => $item['AccountCode'] ?? null,

                    'account_type' => $item['Type'] ?? null,

                    'name' => $item['Name'] ?? null,

                    'synced_at' => now(),
                ]

            );

            if ($model->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->newLine();

        $this->table(
            ['Created', 'Updated'],
            [[
                $created,
                $updated,
            ]]
        );

        $this->info('Account Sync Finished.');

        return self::SUCCESS;
    }
}