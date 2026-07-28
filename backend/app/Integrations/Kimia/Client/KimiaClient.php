<?php

namespace App\Integrations\Kimia\Client;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use App\Integrations\Kimia\Exceptions\KimiaException;

class KimiaClient
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.kimia.base_url'), '/');
        $this->username = config('services.kimia.username');
        $this->password = config('services.kimia.password');
        $this->timeout = config('services.kimia.timeout', 30);
    }

    protected function request()
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->acceptJson()
            ->withBasicAuth($this->username, $this->password);
    }

    public function get(string $uri, array $query = []): Response
{
    $response = $this->request()->get($uri, $query);

    if ($response->failed()) {
        throw new KimiaException(
            "Kimia GET {$uri} failed: {$response->status()} {$response->body()}"
        );
    }

    return $response;
}

    public function post(string $uri, array $data = []): Response
{
    $response = $this->request()->post($uri, $data);

    if ($response->failed()) {
        throw new KimiaException(
            "Kimia POST {$uri} failed: {$response->status()} {$response->body()}"
        );
    }

    return $response;
}

    public function put(string $uri, array $data = []): Response
    {
        return $this->request()->put($uri, $data);
    }

    public function delete(string $uri, array $data = []): Response
    {
        return $this->request()->delete($uri, $data);
    }
}
