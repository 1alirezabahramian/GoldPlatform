<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Integrations\Kimia\Services\KimiaAccountSyncService;

class SyncKimiaAccountsCommand extends Command
{
    protected $signature = 'kimia:sync-accounts';

    protected $description = 'Synchronize Kimia accounts with local database';

    public function __construct(
        protected KimiaAccountSyncService $syncService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $count = $this->syncService->sync();

        $this->newLine();
        $this->info("✔ {$count} accounts synchronized successfully.");
        $this->newLine();

        return self::SUCCESS;
    }
}