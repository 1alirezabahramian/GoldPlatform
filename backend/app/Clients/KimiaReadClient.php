<?php

namespace App\Clients;

use App\Exceptions\KimiaReadException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class KimiaReadClient
{
    public function get(string $endpoint, array $query = []): array
    {
        $response = $this->request()->get($endpoint, $query);

        return $this->decode($response, $endpoint);
    }

    private function request(): PendingRequest
    {
        $request = Http::acceptJson()
            ->baseUrl(rtrim((string) config('services.kimia.base_url'), '/'))
            ->timeout((int) config('services.kimia.timeout', 30))
            ->retry(2, 500, throw: false)
            ->withBasicAuth(
                (string) config('services.kimia.username'),
                (string) config('services.kimia.password')
            );

        $bookId = config('services.kimia.book_id');

        if ($bookId !== null && $bookId !== '') {
            $request = $request->withHeaders([
                'X-Book-Id' => (string) $bookId,
            ]);
        }

        return $request;
    }

    private function decode(Response $response, string $endpoint): array
    {
        if (! $response->successful()) {
            throw new KimiaReadException(
                message: 'Kimia read request failed.',
                status: $response->status(),
                endpoint: $endpoint,
            );
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new KimiaReadException(
                message: 'Kimia read response is not a JSON array or object.',
                status: $response->status(),
                endpoint: $endpoint,
            );
        }

        return $payload;
    }
}
