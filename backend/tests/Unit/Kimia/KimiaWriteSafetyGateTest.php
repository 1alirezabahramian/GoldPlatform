<?php

namespace Tests\Unit\Kimia;

use App\Integrations\Kimia\Client\KimiaClient;
use App\Integrations\Kimia\Exceptions\KimiaWriteDisabledException;
use App\Integrations\Kimia\Safety\KimiaWriteGate;
use App\Services\KimiaService;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KimiaWriteSafetyGateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.kimia', [
            'base_url' => 'https://kimia.test',
            'username' => 'test-user',
            'password' => 'test-password',
            'timeout' => 5,
            'writes_enabled' => false,
        ]);

        Http::fake();
    }

    #[Test]
    public function read_requests_remain_available_in_safe_mode(): void
    {
        Http::fake([
            'https://kimia.test/api/read-only' => Http::response(['ok' => true]),
        ]);

        $result = app(KimiaService::class)->get('/api/read-only');

        $this->assertTrue($result['ok']);
        Http::assertSentCount(1);
    }

    #[Test]
    public function service_write_methods_are_blocked_before_http(): void
    {
        $service = app(KimiaService::class);
        $blocked = 0;

        foreach (['post', 'put', 'delete'] as $method) {
            try {
                $method === 'delete'
                    ? $service->delete('/api/voucher/test')
                    : $service->{$method}('/api/voucher/test', ['test' => true]);

                $this->fail("Kimia {$method} was not blocked.");
            } catch (KimiaWriteDisabledException) {
                $blocked++;
            }
        }

        $this->assertSame(3, $blocked);
        Http::assertNothingSent();
    }

    #[Test]
    public function public_pending_request_cannot_bypass_the_write_gate(): void
    {
        $this->expectException(KimiaWriteDisabledException::class);

        try {
            app(KimiaService::class)
                ->client()
                ->post('/api/account', ['Name' => 'blocked']);
        } finally {
            Http::assertNothingSent();
        }
    }

    #[Test]
    public function legacy_client_write_methods_are_also_blocked(): void
    {
        $client = app(KimiaClient::class);
        $blocked = 0;

        foreach (['post', 'put', 'delete'] as $method) {
            try {
                $client->{$method}('/api/account', ['Name' => 'blocked']);

                $this->fail("Legacy Kimia {$method} was not blocked.");
            } catch (KimiaWriteDisabledException) {
                $blocked++;
            }
        }

        $this->assertSame(3, $blocked);
        Http::assertNothingSent();
    }

    #[Test]
    public function only_an_explicit_true_value_can_open_the_gate(): void
    {
        $gate = app(KimiaWriteGate::class);

        config()->set('services.kimia.writes_enabled', 'false');
        $this->assertFalse($gate->isEnabled());

        config()->set('services.kimia.writes_enabled', 'unexpected');
        $this->assertFalse($gate->isEnabled());

        config()->set('services.kimia.writes_enabled', true);
        $this->assertTrue($gate->isEnabled());
    }
}
