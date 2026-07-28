<?php

namespace App\Integrations\Kimia\Services;

use App\Integrations\Kimia\DTO\AccountDTO;
use App\Integrations\Kimia\Repositories\KimiaAccountRepository;

class KimiaAccountService
{
    public function __construct(
        protected KimiaAccountRepository $repository,
    ) {
    }

    /**
     * @return AccountDTO[]
     */
    public function all(): array
    {
        return $this->repository->all();
    }

    public function find(int $accountId): ?AccountDTO
    {
        return $this->repository->find($accountId);
    }
}