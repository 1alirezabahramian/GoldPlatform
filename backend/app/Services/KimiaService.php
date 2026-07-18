<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KimiaService
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

    public function client(): PendingRequest
    {
        return Http::acceptJson()
            ->baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->retry(2, 500)
            ->withBasicAuth(
                $this->username,
                $this->password
            );
    }

    public function get(string $uri, array $query = []): array
    {
        $response = $this->client()->get($uri, $query);

        $this->log($response);

        $response->throw();

        return $response->json();
    }

    public function post(string $uri, array $data = []): array
    {
        $response = $this->client()->post($uri, $data);

        $this->log($response);

        $response->throw();

        return $response->json();
    }

    public function put(string $uri, array $data = []): array
    {
        $response = $this->client()->put($uri, $data);

        $this->log($response);

        $response->throw();

        return $response->json();
    }

    public function delete(string $uri): array
    {
        $response = $this->client()->delete($uri);

        $this->log($response);

        $response->throw();

        return $response->json();
    }

    protected function log(Response $response): void
    {
        Log::channel('daily')->info('Kimia API', [
            'status' => $response->status(),
            'url'    => $response->effectiveUri()?->__toString(),
        ]);
    }

    public function test(): Response
    {
        return $this->client()->get('/swagger/v1/swagger.json');
    }
}