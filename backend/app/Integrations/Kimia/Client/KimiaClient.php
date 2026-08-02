<?php

namespace App\Integrations\Kimia\Client;

use App\Integrations\Kimia\Exceptions\KimiaException;
use App\Integrations\Kimia\Safety\KimiaWriteGate;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class KimiaClient
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;
    protected int $timeout;

    public function __construct(
        protected KimiaWriteGate $writeGate
    )
    {
        $this->baseUrl = rtrim(
            (string) config('services.kimia.base_url'),
            '/'
        );
        $this->username = (string) config('services.kimia.username');
        $this->password = (string) config('services.kimia.password');
        $this->timeout = (int) config('services.kimia.timeout', 30);
    }

    protected function request(): PendingRequest
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
        $this->writeGate->assertAllowed('POST', $uri);

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
        $this->writeGate->assertAllowed('PUT', $uri);

        return $this->request()->put($uri, $data);
    }

    public function delete(string $uri, array $data = []): Response
    {
        $this->writeGate->assertAllowed('DELETE', $uri);

        return $this->request()->delete($uri, $data);
    }
}
