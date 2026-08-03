<?php

namespace App\Integrations\Kimia\Mappers;

use App\Integrations\Kimia\DTO\AccountDTO;
use InvalidArgumentException;

class KimiaAccountMapper
{
    /**
     * @param array<string, mixed> $account
     */
    public function map(array $account): AccountDTO
    {
        if (! isset($account['AccountId'], $account['Name'], $account['Type'])) {
            throw new InvalidArgumentException(
                'Kimia account payload is missing AccountId, Name, or Type.'
            );
        }

        return new AccountDTO(
            id: (int) $account['AccountId'],
            code: isset($account['AccountCode'])
                ? (int) $account['AccountCode']
                : null,
            name: (string) $account['Name'],
            type: (int) $account['Type'],
            mobile: isset($account['Mobile'])
                ? (string) $account['Mobile']
                : null,
            nationalCode: isset($account['NationalCode'])
                ? (string) $account['NationalCode']
                : null,
            isVisible: (bool) ($account['IsVisible'] ?? true),
            rawData: $account,
        );
    }

    /**
     * @param array<int, mixed> $accounts
     * @return list<AccountDTO>
     */
    public function mapCollection(array $accounts): array
    {
        $mapped = [];

        foreach ($accounts as $account) {
            if (! is_array($account)) {
                continue;
            }

            $mapped[] = $this->map($account);
        }

        return $mapped;
    }
}
