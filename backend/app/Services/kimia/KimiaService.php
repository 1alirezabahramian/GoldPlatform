<?php

namespace App\Services\Kimia;

class KimiaService
{
    public function __construct(
        protected CustomerService $customers,
        protected AccountService $accounts,
    ) {
    }
}