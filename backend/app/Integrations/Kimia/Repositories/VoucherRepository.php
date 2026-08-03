<?php

namespace App\Integrations\Kimia\Repositories;

use App\Integrations\Kimia\Client\KimiaClient;
use InvalidArgumentException;

class VoucherRepository
{
    public function __construct(
        protected KimiaClient $client
    ) {
    }

    public function transactions(
        int $accountId,
        int $pageNumber = 0,
        int $pageSize = 50,
        bool $descending = true
    ): array {
        if ($accountId <= 0) {
            throw new InvalidArgumentException('Kimia account ID must be positive.');
        }

        if ($pageNumber < 0) {
            throw new InvalidArgumentException('Kimia transaction page starts from zero.');
        }

        if ($pageSize <= 0) {
            throw new InvalidArgumentException('Kimia transaction page size must be positive.');
        }

        $response = $this->client->get(
            "/api/voucher/transactions/{$accountId}",
            [
                'pageNumber' => $pageNumber,
                'pageSize' => $pageSize,
                'descending' => $descending ? 'true' : 'false',
            ]
        )->json();

        return is_array($response) ? $response : [];
    }
}
