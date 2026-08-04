<?php

namespace App\Integrations\Kimia\Client;

use App\Integrations\Kimia\Exceptions\KimiaException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KimiaClient
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;
    protected int $timeout;
    protected bool $readOnly;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.kimia.base_url'), '/');
        $this->username = (string) config('services.kimia.username');
        $this->password = (string) config('services.kimia.password');
        $this->timeout = (int) config('services.kimia.timeout', 30);
        $this->readOnly = (bool) config('services.kimia.read_only', true);

        if (
            $this->baseUrl === ''
            || $this->username === ''
            || $this->password === ''
            || $this->timeout <= 0
        ) {
            throw new KimiaException('Kimia API configuration is incomplete.');
        }
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
        return $this->send('GET', $uri, $query);
    }

    public function post(string $uri, array $data = []): Response
    {
        return $this->send('POST', $uri, $data);
    }

    public function put(string $uri, array $data = []): Response
    {
        return $this->send('PUT', $uri, $data);
    }

    public function delete(string $uri, array $data = []): Response
    {
        return $this->send('DELETE', $uri, $data);
    }

    private function send(string $method, string $uri, array $payload = []): Response
    {
        if ($this->readOnly && $method !== 'GET') {
            Log::warning('Blocked Kimia write request while read-only mode is enabled.', [
                'method' => $method,
                'uri' => $uri,
            ]);

            throw new KimiaException('Kimia write operations are disabled in read-only mode.');
        }

        try {
            $response = match ($method) {
                'GET' => $this->request()->get($uri, $payload),
                'POST' => $this->request()->post($uri, $payload),
                'PUT' => $this->request()->put($uri, $payload),
                'DELETE' => $this->request()->delete($uri, $payload),
                default => throw new KimiaException('Unsupported Kimia HTTP method.'),
            };
        } catch (ConnectionException $exception) {
            Log::warning('Kimia API connection failed.', [
                'method' => $method,
                'uri' => $uri,
                'exception' => $exception::class,
            ]);

            throw new KimiaException(
                "Kimia {$method} {$uri} connection failed.",
                previous: $exception
            );
        }

        Log::info('Kimia API request completed.', [
            'method' => $method,
            'uri' => $uri,
            'status' => $response->status(),
        ]);

        if ($response->failed()) {
            throw new KimiaException(
                "Kimia {$method} {$uri} failed with HTTP {$response->status()}."
            );
        }

        return $response;
    }
}
