<?php

namespace App\Repositories\Kimia\Read;

use App\Clients\KimiaReadClient;

final class BalanceReadRepository
{
    public function __construct(private readonly KimiaReadClient $client) {}

    public function forAccount(int $accountId, bool $includePeaks = false): array
    {
        return $this->client->get("/api/voucher/balance/{$accountId}", [
            'includePeaks' => $includePeaks ? 'true' : 'false',
        ]);
    }
}
