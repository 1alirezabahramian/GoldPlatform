<?php

namespace Tests\Feature;

use App\Clients\KimiaReadClient;
use App\Exceptions\KimiaReadException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KimiaReadClientTest extends TestCase
{
    public function test_it_performs_only_a_get_request_with_optional_book_header(): void
    {
        config()->set('services.kimia.base_url', 'https://kimia.test');
        config()->set('services.kimia.username', 'user');
        config()->set('services.kimia.password', 'pass');
        config()->set('services.kimia.book_id', '42');

        Http::fake([
            'https://kimia.test/api/account*' => Http::response([
                ['AccountId' => 350],
            ], 200),
        ]);

        $result = app(KimiaReadClient::class)->get('/api/account', ['Type' => 3]);

        $this->assertSame([['AccountId' => 350]], $result);

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'GET'
                && $request->url() === 'https://kimia.test/api/account?Type=3'
                && $request->hasHeader('X-Book-Id', '42');
        });
    }

    public function test_it_does_not_convert_http_failure_to_an_empty_result(): void
    {
        config()->set('services.kimia.base_url', 'https://kimia.test');
        config()->set('services.kimia.username', 'user');
        config()->set('services.kimia.password', 'pass');

        Http::fake([
            'https://kimia.test/api/account*' => Http::response([
                'message' => 'Unauthorized',
            ], 401),
        ]);

        try {
            app(KimiaReadClient::class)->get('/api/account', ['Type' => 3]);
            $this->fail('KimiaReadException was not thrown.');
        } catch (KimiaReadException $exception) {
            $this->assertSame(401, $exception->status);
            $this->assertSame('/api/account', $exception->endpoint);
        }
    }

    public function test_it_rejects_a_non_json_payload(): void
    {
        config()->set('services.kimia.base_url', 'https://kimia.test');
        config()->set('services.kimia.username', 'user');
        config()->set('services.kimia.password', 'pass');

        Http::fake([
            'https://kimia.test/api/account*' => Http::response('invalid', 200),
        ]);

        $this->expectException(KimiaReadException::class);

        app(KimiaReadClient::class)->get('/api/account');
    }
}
