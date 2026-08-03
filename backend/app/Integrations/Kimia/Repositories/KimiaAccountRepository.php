<?php

namespace App\Integrations\Kimia\Repositories;

use App\Integrations\Kimia\Client\KimiaClient;
use App\Integrations\Kimia\DTO\AccountDTO;
use App\Integrations\Kimia\Mappers\KimiaAccountMapper;

class KimiaAccountRepository
{
    public function __construct(
        protected KimiaClient $client,
        protected KimiaAccountMapper $mapper,
    ) {
    }

    /**
     * @return list<AccountDTO>
     */
    public function all(?int $accountType = null): array
    {
        $query = $accountType === null
            ? []
            : ['Type' => $accountType];

        $response = $this->client
            ->get('/api/account', $query)
            ->json();

        return $this->mapper->mapCollection(
            $this->accountRows(is_array($response) ? $response : [])
        );
    }

    public function find(int $accountId): ?AccountDTO
    {
        return collect($this->all())
            ->first(fn (AccountDTO $account): bool => $account->id === $accountId);
    }

    /**
     * @param array<string, mixed>|list<mixed> $response
     * @return array<int, mixed>
     */
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
