<?php

namespace App\Clients;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KimiaClient
{
    protected PendingRequest $http;

    public function __construct()
    {
        $this->http = Http::baseUrl(config('services.kimia.url'))
            ->acceptJson()
            ->contentType('application/json')
            ->timeout(30)
            ->retry(3, 500);
    }

    public function get(string $uri, array $query = []): Response
    {
        return $this->request('GET', $uri, $query);
    }

    public function post(string $uri, array $data = []): Response
    {
        return $this->request('POST', $uri, $data);
    }

    public function put(string $uri, array $data = []): Response
    {
        return $this->request('PUT', $uri, $data);
    }

    public function delete(string $uri, array $data = []): Response
    {
        return $this->request('DELETE', $uri, $data);
    }

    protected function request(string $method, string $uri, array $data = []): Response
    {
        Log::info('Kimia Request', [
            'method' => $method,
            'uri' => $uri,
        ]);

        $response = match ($method) {
            'GET'    => $this->http->get($uri, $data),
            'POST'   => $this->http->post($uri, $data),
            'PUT'    => $this->http->put($uri, $data),
            'DELETE' => $this->http->delete($uri, $data),
        };

        Log::info('Kimia Response', [
            'status' => $response->status(),
        ]);

        return $response;
    }
}