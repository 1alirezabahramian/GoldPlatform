<?php

namespace App\Repositories\Kimia\Read;

use App\Clients\KimiaReadClient;

final class ProductReadRepository
{
    public function __construct(private readonly KimiaReadClient $client) {}

    public function coins(): array
    {
        return $this->client->get('/api/product/coins');
    }

    public function currencies(): array
    {
        return $this->client->get('/api/product/currencies');
    }
}
