<?php

namespace App\Services\Kimia;

use Illuminate\Support\Facades\Http;

class KimiaService
{
    protected string $baseUrl;

    protected string $username;

    protected string $password;

    public function __construct()
    {
        $this->baseUrl = config('services.kimia.url');

        $this->username = config('services.kimia.username');

        $this->password = config('services.kimia.password');
    }
}