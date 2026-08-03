<?php

namespace Tests\Unit\Kimia;

use App\Integrations\Kimia\Client\KimiaClient;
use App\Integrations\Kimia\Exceptions\KimiaException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KimiaClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.kimia', [
            'base_url' => 'https://kimia.test',
            'username' => 'test-user',
            'password' => 'test-password',
            'timeout' => 5,
        ]);
    }

    #[Test]
    public function all_http_methods_use_the_canonical_client(): void
    {
        Http::fake([
            'https://kimia.test/*' => Http::response(['ok' => true]),
        ]);

        $client = app(KimiaClient::class);

        $client->get('/get', ['q' => '1']);
        $client->post('/post', ['value' => 1]);
        $client->put('/put', ['value' => 2]);
        $client->delete('/delete', ['value' => 3]);

        Http::assertSentCount(4);

        foreach (['GET', 'POST', 'PUT', 'DELETE'] as $method) {
            Http::assertSent(
                fn (Request $request): bool => $request->method() === $method
            );
        }
    }

    #[Test]
    public function failed_responses_use_a_safe_consistent_exception(): void
    {
        Http::fake([
            'https://kimia.test/api/account' => Http::response([
                'password' => 'must-not-leak',
                'detail' => 'sensitive upstream response',
            ], 500),
        ]);

        try {
            app(KimiaClient::class)->get('/api/account');
            $this->fail('Expected KimiaException was not thrown.');
        } catch (KimiaException $exception) {
            $this->assertSame(
                'Kimia GET /api/account failed with HTTP 500.',
                $exception->getMessage()
            );
            $this->assertStringNotContainsString(
                'must-not-leak',
                $exception->getMessage()
            );
        }
    }

    #[Test]
    public function request_logs_do_not_include_credentials_or_payloads(): void
    {
        Log::spy();

        Http::fake([
            'https://kimia.test/api/account' => Http::response(['ok' => true]),
        ]);

        app(KimiaClient::class)->post('/api/account', [
            'NationalCode' => 'secret-national-code',
        ]);

        Log::shouldHaveReceived('info')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'Kimia API request completed.'
                    && $context === [
                        'method' => 'POST',
                        'uri' => '/api/account',
                        'status' => 200,
                    ];
            });
    }

    #[Test]
    public function incomplete_configuration_is_rejected(): void
    {
        config()->set('services.kimia.password', '');

        $this->expectException(KimiaException::class);
        $this->expectExceptionMessage('Kimia API configuration is incomplete.');

        new KimiaClient();
    }
}
