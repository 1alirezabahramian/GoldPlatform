<?php

namespace App\Clients;

use App\Exceptions\KimiaReadException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class KimiaReadClient
{
    public function get(string $endpoint, array $query = []): array
    {
        $response = $this->request($endpoint)->get($endpoint, $query);

        return $this->decode($response, $endpoint);
    }

    private function request(string $endpoint): PendingRequest
    {
        $timeout = (int) config('services.kimia.timeout', 30);

        foreach ((array) config('services.kimia.timeout_profiles', []) as $prefix => $profileTimeout) {
            if (str_starts_with($endpoint, (string) $prefix)) {
                $timeout = (int) $profileTimeout;
                break;
            }
        }

        return Http::acceptJson()
            ->baseUrl(rtrim((string) config('services.kimia.base_url'), '/'))
            ->timeout($timeout)
            ->retry(
                (int) config('services.kimia.read_retries', 2),
                (int) config('services.kimia.retry_delay_ms', 250),
                throw: false,
            )
            ->withBasicAuth(
                (string) config('services.kimia.username'),
                (string) config('services.kimia.password'),
            );
    }

    private function decode(Response $response, string $endpoint): array
    {
        if (! $response->successful()) {
            throw new KimiaReadException('Kimia read request failed.', $response->status(), $endpoint);
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new KimiaReadException('Kimia read response is not a JSON array or object.', $response->status(), $endpoint);
        }

        return $payload;
    }
}
