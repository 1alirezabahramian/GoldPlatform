<?php

namespace App\Repositories\Kimia\Read;

use App\Clients\KimiaReadClient;

final class AccountReadRepository
{
    public function __construct(private readonly KimiaReadClient $client) {}

    public function groups(): array
    {
        return $this->client->get('/api/account/groups');
    }

    public function retailAccounts(): array
    {
        return $this->client->get('/api/account', ['Type' => 3]);
    }

    public function find(int $id): array
    {
        return $this->client->get("/api/account/{$id}");
    }
}
