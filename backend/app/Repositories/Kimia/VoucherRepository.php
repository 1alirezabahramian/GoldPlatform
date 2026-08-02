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
                'descending' => $descending,
            ]
        );
    }
}
