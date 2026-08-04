<?php

namespace Tests\Feature;

use App\Repositories\Kimia\Read\BalanceReadRepository;
use App\Repositories\Kimia\Read\ProductReadRepository;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KimiaCatalogAndBalanceReadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.kimia.base_url', 'https://kimia.test');
        config()->set('services.kimia.username', 'test-user');
        config()->set('services.kimia.password', 'test-pass');
        config()->set('services.kimia.book_id', null);

        Http::preventStrayRequests();
    }

    public function test_it_reads_dynamic_coins_without_local_mapping(): void
    {
        Http::fake([
            'https://kimia.test/api/product/coins' => Http::response([
                ['CoinId' => 16, 'Name' => 'Imami', 'Fineness' => 900],
            ]),
        ]);

        $coins = app(ProductReadRepository::class)->coins();

        $this->assertSame(16, $coins[0]['CoinId']);
        Http::assertSent(fn ($request) =>
            $request->method() === 'GET'
            && $request->url() === 'https://kimia.test/api/product/coins'
        );
    }

    public function test_it_reads_dynamic_currencies_without_local_mapping(): void
    {
        Http::fake([
            'https://kimia.test/api/product/currencies' => Http::response([
                ['CurrencyId' => 12, 'Name' => 'Dollar', 'IsVisible' => true],
            ]),
        ]);

        $currencies = app(ProductReadRepository::class)->currencies();

        $this->assertSame(12, $currencies[0]['CurrencyId']);
        Http::assertSent(fn ($request) =>
            $request->method() === 'GET'
            && $request->url() === 'https://kimia.test/api/product/currencies'
        );
    }

    public function test_it_reads_balance_with_confirmed_include_peaks_query(): void
    {
        Http::fake([
            'https://kimia.test/api/voucher/balance/350*' => Http::response([
                ['Money' => '100000000', 'CurrencyId' => 11, 'CurrencySymbol' => 'IRR'],
            ]),
        ]);

        $balances = app(BalanceReadRepository::class)->forAccount(350);

        $this->assertSame('100000000', $balances[0]['Money']);
        Http::assertSent(fn ($request) =>
            $request->method() === 'GET'
            && $request->url() === 'https://kimia.test/api/voucher/balance/350?includePeaks=false'
        );
    }

    public function test_read_repositories_do_not_expose_write_methods(): void
    {
        foreach ([
            app(ProductReadRepository::class),
            app(BalanceReadRepository::class),
        ] as $repository) {
            $this->assertFalse(method_exists($repository, 'create'));
            $this->assertFalse(method_exists($repository, 'update'));
            $this->assertFalse(method_exists($repository, 'delete'));
        }
    }
}
