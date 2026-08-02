<?php

namespace App\Services;

use App\Integrations\Kimia\Safety\KimiaWriteGate;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

class KimiaService
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

        if (
            $this->baseUrl === ''
            || $this->username === ''
            || $this->password === ''
        ) {
            throw new RuntimeException(
                'Kimia API configuration is incomplete.'
            );
        }
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
            )
            ->withRequestMiddleware(function (
                RequestInterface $request
            ): RequestInterface {
                if (! in_array(
                    strtoupper($request->getMethod()),
                    ['GET', 'HEAD'],
                    true
                )) {
                    $this->writeGate->assertAllowed(
                        $request->getMethod(),
                        $request->getUri()->getPath()
                    );
                }

                return $request;
            });
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
        $this->writeGate->assertAllowed('POST', $uri);

        $response = $this->client()->post($uri, $data);

        $this->log($response);

        $response->throw();

        return $response->json();
    }

    public function put(string $uri, array $data = []): array
    {
        $this->writeGate->assertAllowed('PUT', $uri);

        $response = $this->client()->put($uri, $data);

        $this->log($response);

        $response->throw();

        return $response->json();
    }

    public function delete(string $uri): array
    {
        $this->writeGate->assertAllowed('DELETE', $uri);

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
