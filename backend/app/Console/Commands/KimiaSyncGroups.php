<?php

namespace App\Console\Commands;

use App\Models\AccountGroup;
use App\Repositories\Kimia\AccountRepository;
use Illuminate\Console\Command;
use Throwable;

class KimiaSyncGroups extends Command
{
    protected $signature = 'kimia:sync-groups';

    protected $description = 'Synchronize account groups from Kimia';

    public function __construct(
        protected AccountRepository $repository
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Connecting to Kimia...');

        $received = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;

        try {
            foreach ([1, 3, 5, 6, 8, 9, 10, 11, 12] as $type) {
                $groups = $this->repository->groups($type);

                foreach ($groups as $group) {
                    if (
                        ! is_array($group)
                        || ! isset(
                            $group['Id'],
                            $group['Name'],
                            $group['AccountType']
                        )
                    ) {
                        $skipped++;

                        continue;
                    }

                    $received++;

                    $model = AccountGroup::query()
                        ->where('kimia_id', (int) $group['Id'])
                        ->first();

                    $data = [
                        'account_type' => (int) $group['AccountType'],
                        'name' => (string) $group['Name'],
                        'is_active' => true,
                    ];

                    if ($model === null) {
                        AccountGroup::create([
                            'kimia_id' => (int) $group['Id'],
                            ...$data,
                            'synced_at' => now(),
                        ]);

                        $created++;

                        continue;
                    }

                    $model->fill($data);

                    if (! $model->isDirty()) {
                        $skipped++;

                        continue;
                    }

                    $model->synced_at = now();
                    $model->save();

                    $updated++;
                }
            }
        } catch (Throwable $exception) {
            report($exception);

            $this->error(
                'Kimia group synchronization failed: '
                .$exception->getMessage()
            );

            return self::FAILURE;
        }

        $this->newLine();

        $this->table(
            ['Received', 'Created', 'Updated', 'Skipped'],
            [[
                $received,
                $created,
                $updated,
                $skipped,
            ]]
        );

        $this->info('Kimia group synchronization finished.');

        return self::SUCCESS;
    }
}