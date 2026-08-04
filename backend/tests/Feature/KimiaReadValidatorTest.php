<?php

namespace Tests\Feature;

use App\Integrations\Kimia\Services\KimiaReadValidatorService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class KimiaReadValidatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.kimia', [
            'base_url' => 'https://kimia.test',
            'username' => 'test-user',
            'password' => 'test-password',
            'timeout' => 5,
            'read_only' => true,
            'read_retries' => 0,
            'retry_delay_ms' => 0,
            'timeout_profiles' => [],
        ]);
    }

    #[Test]
    public function validator_checks_all_confirmed_read_endpoints_without_exposing_payloads(): void
    {
        Http::fake([
            'https://kimia.test/api/account*' => Http::response([
                ['AccountId' => 350, 'Type' => 3, 'Name' => 'Retail'],
            ]),
            'https://kimia.test/api/product/coins*' => Http::response([
                ['CoinId' => 16, 'Name' => 'Coin', 'Fineness' => 900],
            ]),
            'https://kimia.test/api/product/currencies*' => Http::response([
                ['CurrencyId' => 12, 'Name' => 'USD', 'IsVisible' => true],
            ]),
            'https://kimia.test/api/barcode*' => Http::response([]),
            'https://kimia.test/api/voucher/balance/350*' => Http::response([
                ['CurrencyId' => 11, 'CurrencySymbol' => 'IRR', 'Money' => 1000],
            ]),
            'https://kimia.test/api/voucher/transactions/350*' => Http::response([
                'Items' => [['RecordId' => 1, 'Action' => 64]],
            ]),
        ]);

        $result = app(KimiaReadValidatorService::class)->validate(350);

        $this->assertTrue($result['ok']);
        $this->assertSame('pass', $result['endpoints']['accounts']['status']);
        $this->assertSame('list', $result['endpoints']['accounts']['shape']);
        $this->assertSame('wrapped:Items', $result['endpoints']['transactions']['shape']);
        $this->assertCount(6, $result['endpoints']);

        Http::assertSent(fn (Request $request): bool => $request->method() === 'GET');
        Http::assertSentCount(6);
    }

    #[Test]
    public function validator_reports_safe_error_categories_instead_of_sensitive_messages(): void
    {
        Http::fake([
            'https://kimia.test/api/account*' => Http::response(['secret' => 'hidden'], 401),
            '*' => Http::response([]),
        ]);

        $result = app(KimiaReadValidatorService::class)->validate();

        $this->assertFalse($result['ok']);
        $this->assertSame('unauthorized', $result['endpoints']['accounts']['error_type']);
        $this->assertArrayNotHasKey('message', $result['endpoints']['accounts']);
    }
}
