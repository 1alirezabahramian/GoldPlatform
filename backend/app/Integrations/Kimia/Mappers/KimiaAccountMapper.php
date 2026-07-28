<?php

namespace App\Integrations\Kimia\Mappers;

use App\Integrations\Kimia\DTO\AccountDTO;

class KimiaAccountMapper
{
    public function map(array $account): AccountDTO
    {
        return new AccountDTO(
            id: (int) $account['AccountId'],
            code: (int) $account['AccountCode'],
            name: (string) $account['Name'],
            type: (int) $account['Type'],
            mobile: $account['Mobile'] ?? null,
            nationalCode: $account['NationalCode'] ?? null,
            isVisible: $account['IsVisible'] ?? true,
        );
    }

    public function mapCollection(array $accounts): array
    {
        return array_map(
            fn (array $account) => $this->map($account),
            $accounts
        );
    }
}