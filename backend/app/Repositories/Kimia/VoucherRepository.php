<?php

namespace App\Repositories\Kimia;

use App\Services\KimiaService;
use InvalidArgumentException;

class VoucherRepository
{
    public function __construct(
        protected KimiaService $kimia
    ) {
    }

    public function balance(
        int $accountId,
        ?bool $includePeaks = null
    ): array {
        if ($accountId <= 0) {
            throw new InvalidArgumentException('Kimia account ID must be positive.');
        }

        $query = [];

        if ($includePeaks !== null) {
            // Kimia requires ASP.NET boolean literals rather than Guzzle's
            // default integer serialization for PHP booleans.
            $query['includePeaks'] = $includePeaks ? 'true' : 'false';
        }

        return $this->kimia->get(
            "/api/voucher/balance/{$accountId}",
            $query
        );
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

        return $this->kimia->get(
            "/api/voucher/transactions/{$accountId}",
            [
                'pageNumber' => $pageNumber,
                'pageSize' => $pageSize,
                // Kimia's ASP.NET binder accepts the boolean query literals
                // "true"/"false". Laravel/Guzzle serializes a PHP boolean as
                // "1"/"0", which Kimia rejects with HTTP 400.
                'descending' => $descending ? 'true' : 'false',
            ]
        );
    }
}
