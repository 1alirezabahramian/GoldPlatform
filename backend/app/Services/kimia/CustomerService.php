<?php

namespace App\Services\Kimia;

class CustomerService
{
    public function __construct(
        protected KimiaClient $client
    ) {
    }
}
public function findByMobile(string $mobile)
{
    return $this->client->get('customers', [
        'mobile' => $mobile,
    ]);
}

public function create(array $data)
{
    return $this->client->post('customers', $data);
}