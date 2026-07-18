<?php

namespace App\Console\Commands;

use App\Models\AccountGroup;
use App\Repositories\Kimia\AccountRepository;
use Illuminate\Console\Command;

class KimiaSyncGroups extends Command
{
    protected $signature = 'kimia:sync-groups';

    protected $description = 'Sync Account Groups from Kimia';

    public function __construct(
        protected AccountRepository $repository
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Connecting to Kimia...');

        $created = 0;
        $updated = 0;

        foreach ([
            1, 3, 5, 6, 8, 9, 10, 11, 12
        ] as $type) {

            $groups = $this->repository->allGroups($type);

            foreach ($groups as $group) {

                $model = AccountGroup::updateOrCreate(

                    [
                        'kimia_id' => $group['Id'],
                    ],

                    [
                        'name'         => $group['Name'],
                        'account_type' => $group['AccountType'],
                        'is_active'    => true,
                        'synced_at'    => now(),
                    ]

                );

                if ($model->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }
        }

        $this->newLine();

        $this->table(
            ['Created', 'Updated'],
            [
                [$created, $updated]
            ]
        );

        $this->info('Kimia Sync Finished.');

        return self::SUCCESS;
    }
}