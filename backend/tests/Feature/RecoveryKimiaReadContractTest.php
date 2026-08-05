<?php

namespace Tests\Feature;

use App\Exceptions\KimiaReadException;
use App\Repositories\Kimia\Read\AccountReadRepository;
use App\Repositories\Kimia\Read\BalanceReadRepository;
use App\Repositories\Kimia\Read\ProductReadRepository;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class RecoveryKimiaReadContractTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.kimia.base_url', 'https://kimia.test');
        config()->set('services.kimia.username', 'user');
        config()->set('services.kimia.password', 'secret');
        config()->set('services.kimia.read_retries', 0);
    }

    public function test_retail_accounts_use_confirmed_type_parameter(): void
    {
        Http::fake(['https://kimia.test/api/account*' => Http::response([], 200)]);

        app(AccountReadRepository::class)->retailAccounts();

        Http::assertSent(fn ($request) => $request->url() === 'https://kimia.test/api/account?Type=3');
    }

    public function test_balance_coin_and_currency_reads_use_confirmed_endpoints(): void
    {
        Http::fake([
            'https://kimia.test/api/voucher/balance/350*' => Http::response([], 200),
            'https://kimia.test/api/product/coins' => Http::response([], 200),
            'https://kimia.test/api/product/currencies' => Http::response([], 200),
        ]);

        app(BalanceReadRepository::class)->forAccount(350);
        app(ProductReadRepository::class)->coins();
        app(ProductReadRepository::class)->currencies();

        Http::assertSentCount(3);
    }

    public function test_read_failure_is_not_converted_to_empty_success(): void
    {
        Http::fake(['https://kimia.test/api/product/coins' => Http::response(['error' => 'down'], 503)]);

        $this->expectException(KimiaReadException::class);

        app(ProductReadRepository::class)->coins();
    }
}
