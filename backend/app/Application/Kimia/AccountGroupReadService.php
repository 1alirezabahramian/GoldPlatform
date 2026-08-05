<?php

namespace App\Application\Kimia;

use App\Integrations\Kimia\Repositories\KimiaAccountRepository;

final class AccountGroupReadService
{
    public function __construct(
        private readonly KimiaAccountRepository $accounts
    ) {
    }

    /**
     * Return the account-group catalog through the application boundary.
     *
     * @return array<int|string, mixed>
     */
    public function read(): array
    {
        return $this->accounts->groups();
    }
}
