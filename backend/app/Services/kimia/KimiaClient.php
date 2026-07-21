<?php

namespace App\Services\Kimia;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class KimiaClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('kimia.base_url'), '/');
    }

    public function get(string $uri, array $query = []): Response
    {
        return Http::timeout(config('kimia.timeout'))
            ->acceptJson()
            ->get($this->baseUrl.'/'.$uri, $query);
    }

    public function post(string $uri, array $data = []): Response
    {
        return Http::timeout(config('kimia.timeout'))
            ->acceptJson()
            ->post($this->baseUrl.'/'.$uri, $data);
    }
}