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
     * @return list<AccountDTO>
     */
    public function all(?int $accountType = null): array
    {
        return $this->repository->all($accountType);
    }

    public function find(int $accountId): ?AccountDTO
    {
        return $this->repository->find($accountId);
    }
}
