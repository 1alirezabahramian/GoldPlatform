<?php

namespace App\Integrations\Kimia\Repositories;

use App\Integrations\Kimia\Client\KimiaClient;
use App\Integrations\Kimia\Mappers\KimiaAccountMapper;

class KimiaAccountRepository
{
    public function __construct(
        protected KimiaClient $client,
        protected KimiaAccountMapper $mapper,
    ) {
    }

    public function all(): array
    {
        $accounts = $this->client
            ->get('/api/account')
            ->json();

        return $this->mapper->mapCollection($accounts);
    }

    public function find(int $accountId)
    {
        return collect($this->all())
            ->first(fn ($account) => $account->id === $accountId);
    }
}