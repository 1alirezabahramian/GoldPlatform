<?php

namespace App\Repositories\Kimia;

use App\Services\KimiaService;

class AccountRepository
{
    public function __construct(
        protected KimiaService $kimia
    ) {
    }

    public function allGroups(int $accountType): array
    {
        $response = $this->kimia
            ->client()
            ->get('/api/account/groups', [
                'accountType' => $accountType,
            ]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json() ?? [];
    }
}