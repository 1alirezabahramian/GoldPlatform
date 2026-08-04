<?php

namespace App\Repositories\Kimia\Read;

use App\Clients\KimiaReadClient;

class ProductReadRepository
{
    public function __construct(
        private readonly KimiaReadClient $client,
    ) {
    }

    /**
     * @return array<int, mixed>
     */
    public function coins(): array
    {
        return $this->client->get('/api/product/coins');
    }

    /**
     * @return array<int, mixed>
     */
    public function currencies(): array
    {
        return $this->client->get('/api/product/currencies');
    }
}
