<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

class KimiaService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.kimia.base_url');
        $this->username = config('services.kimia.username');
        $this->password = config('services.kimia.password');
        $this->timeout = config('services.kimia.timeout', 30);
    }

    protected function client(): PendingRequest
    {
        return Http::acceptJson()
            ->baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->withBasicAuth(
                $this->username,
                $this->password
            );
    }

    public function test(): Response
    {
        return $this->client()->get('/swagger/v1/swagger.json');
    }
}