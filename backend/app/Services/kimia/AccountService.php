<?php

namespace App\Services\Kimia;

class AccountService
{
    public function __construct(
        protected KimiaClient $client
    ) {
    }

    public function accountGroups()
    {
        return $this->client->get('account-groups');
    }
}
public function accountGroups()
{
    return $this->client
        ->get('account-groups')
        ->json();
}