<?php

namespace App\Repositories\Kimia\Read;

use App\Clients\KimiaReadClient;

class BalanceReadRepository
{
    public function __construct(
        private readonly KimiaReadClient $client,
    ) {
    }

    /**
     * Return confirmed Kimia voucher balances for one account.
     *
     * @return array<int, mixed>
     */
    public function forAccount(int $accountId, bool $includePeaks = false): array
    {
        return $this->client->get("/api/voucher/balance/{$accountId}", [
            'includePeaks' => $includePeaks ? 'true' : 'false',
        ]);
    }
}
