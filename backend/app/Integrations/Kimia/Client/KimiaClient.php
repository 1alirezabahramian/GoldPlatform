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
    protected int $readRetries;
    protected int $retryDelayMs;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.kimia.base_url'), '/');
        $this->username = (string) config('services.kimia.username');
        $this->password = (string) config('services.kimia.password');
        $this->timeout = (int) config('services.kimia.timeout', 30);
        $this->readOnly = (bool) config('services.kimia.read_only', true);
        $this->readRetries = max(0, (int) config('services.kimia.read_retries', 2));
        $this->retryDelayMs = max(0, (int) config('services.kimia.retry_delay_ms', 250));

        if (
            $this->baseUrl === ''
            || $this->username === ''
            || $this->password === ''
            || $this->timeout <= 0
        ) {
            throw new KimiaException('Kimia API configuration is incomplete.');
        }
    }

    protected function request(int $timeout): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($timeout)
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

        $attempts = $method === 'GET' ? $this->readRetries + 1 : 1;
        $timeout = $this->timeoutFor($uri);
        $lastConnectionException = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $response = match ($method) {
                    'GET' => $this->request($timeout)->get($uri, $payload),
                    'POST' => $this->request($timeout)->post($uri, $payload),
                    'PUT' => $this->request($timeout)->put($uri, $payload),
                    'DELETE' => $this->request($timeout)->delete($uri, $payload),
                    default => throw new KimiaException('Unsupported Kimia HTTP method.'),
                };

                Log::info('Kimia API request completed.', [
                    'method' => $method,
                    'uri' => $uri,
                    'status' => $response->status(),
                    'attempt' => $attempt,
                ]);

                if ($response->failed()) {
                    throw new KimiaException(
                        "Kimia {$method} {$uri} failed with HTTP {$response->status()}."
                    );
                }

                return $response;
            } catch (ConnectionException $exception) {
                $lastConnectionException = $exception;

                Log::warning('Kimia API connection failed.', [
                    'method' => $method,
                    'uri' => $uri,
                    'attempt' => $attempt,
                    'max_attempts' => $attempts,
                    'exception' => $exception::class,
                ]);

                if ($attempt < $attempts && $this->retryDelayMs > 0) {
                    usleep($this->retryDelayMs * 1000);
                }
            }
        }

        throw new KimiaException(
            "Kimia {$method} {$uri} connection failed after {$attempts} attempt(s).",
            previous: $lastConnectionException
        );
    }

    private function timeoutFor(string $uri): int
    {
        $profiles = (array) config('services.kimia.timeout_profiles', []);

        foreach ($profiles as $prefix => $timeout) {
            if (str_starts_with($uri, (string) $prefix) && (int) $timeout > 0) {
                return (int) $timeout;
            }
        }

        return $this->timeout;
    }
}
