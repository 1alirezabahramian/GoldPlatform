<?php

namespace App\Repositories\Kimia\Read;

use App\Clients\KimiaReadClient;

class AccountReadRepository
{
    public function __construct(
        private readonly KimiaReadClient $client,
    ) {
    }

    /**
     * Return Kimia account groups for the optional confirmed accountType filter.
     *
     * @return array<int, mixed>
     */
    public function groups(?int $accountType = null): array
    {
        $query = $accountType === null
            ? []
            : ['accountType' => $accountType];

        return $this->client->get('/api/account/groups', $query);
    }

    /**
     * Return retail accounts. Type=3 is confirmed by the project evidence.
     *
     * @return array<int, mixed>
     */
    public function retailAccounts(): array
    {
        return $this->client->get('/api/account', [
            'Type' => 3,
        ]);
    }

    /**
     * Return one account by its Kimia id.
     *
     * @return array<string, mixed>
     */
    public function find(int $id): array
    {
        return $this->client->get("/api/account/{$id}");
    }
}
