<?php

namespace App\Integrations\Kimia\Services;

use App\Integrations\Kimia\Adapters\AccountAdapter;
use App\Models\ExternalAccount;
use Illuminate\Support\Carbon;

class KimiaAccountSyncService
{
    public function __construct(
        protected KimiaAccountService $accountService,
        protected AccountAdapter $adapter,
    ) {
    }

    public function sync(): int
    {
        $accounts = $this->accountService->all();

        foreach ($accounts as $account) {

            $data = $this->adapter->toArray($account);

            ExternalAccount::updateOrCreate(
                [
                    'provider'    => 'kimia',
                    'external_id' => $data['external_id'],
                ],
                [
                    'code'           => $data['code'],
                    'name'           => $data['name'],
                    'type'           => $data['type'],
                    'mobile'         => $data['mobile'],
                    'national_id'    => $data['national_id'],
                    'is_active'      => $data['is_active'],
                    'raw_data'       => $data,
                    'sync_hash'      => hash('sha256', json_encode($data)),
                    'sync_status'    => 'synced',
                    'sync_error'     => null,
                    'last_synced_at' => Carbon::now(),
                ]
            );
        }

        return count($accounts);
    }
}