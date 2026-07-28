<?php

namespace App\Integrations\Kimia\Adapters;

use App\Integrations\Kimia\DTO\AccountDTO;

class AccountAdapter
{
    public function toArray(AccountDTO $account): array
    {
        return [
            'external_id' => $account->id,
            'code'        => $account->code,
            'name'        => $account->name,
            'type'        => $account->type,
            'mobile'      => $account->mobile,
            'national_id' => $account->nationalCode,
            'is_active'   => $account->isVisible,
        ];
    }
}
