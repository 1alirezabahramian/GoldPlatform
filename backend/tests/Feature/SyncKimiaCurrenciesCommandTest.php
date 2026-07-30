<?php

namespace Tests\Feature;

use App\Models\KimiaCurrency;
use App\Services\KimiaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SyncKimiaCurrenciesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_and_updates_kimia_currencies_without_duplicates(): void
    {
        $kimia = Mockery::mock(KimiaService::class);

        $kimia->shouldReceive('get')
            ->twice()
            ->with('/api/product/currencies')
            ->andReturn(
                [
                    [
                        'CurrencyId' => 11,
                        'Name' => 'Rial',
                        'IsVisible' => true,
                    ],
                    [
                        'CurrencyId' => 12,
                        'Name' => 'US Dollar',
                        'IsVisible' => true,
                    ],
                ],
                [
                    [
                        'CurrencyId' => 11,
                        'Name' => 'Iranian Rial',
                        'IsVisible' => false,
                    ],
                    [
                        'CurrencyId' => 12,
                        'Name' => 'US Dollar',
                        'IsVisible' => true,
                    ],
                ]
            );

        $this->app->instance(KimiaService::class, $kimia);

        $this->artisan('kimia:sync-currencies')
            ->assertSuccessful();

        $this->assertDatabaseCount('kimia_currencies', 2);

        $this->artisan('kimia:sync-currencies')
            ->assertSuccessful();

        $this->assertDatabaseCount('kimia_currencies', 2);

        $currency = KimiaCurrency::query()
            ->where('kimia_id', 11)
            ->firstOrFail();

        $this->assertSame('Iranian Rial', $currency->name);
        $this->assertFalse($currency->is_visible);
        $this->assertNotNull($currency->synced_at);
    }

    public function test_it_skips_unchanged_kimia_currencies(): void
    {
        $currency = KimiaCurrency::create([
            'kimia_id' => 12,
            'name' => 'US Dollar',
            'is_visible' => true,
            'synced_at' => now()->subDay(),
        ]);

        $originalUpdatedAt = $currency->updated_at;
        $originalSyncedAt = $currency->synced_at;

        $kimia = Mockery::mock(KimiaService::class);

        $kimia->shouldReceive('get')
            ->once()
            ->with('/api/product/currencies')
            ->andReturn([[
                'CurrencyId' => 12,
                'Name' => 'US Dollar',
                'IsVisible' => true,
            ]]);

        $this->app->instance(KimiaService::class, $kimia);

        $this->artisan('kimia:sync-currencies')
            ->assertSuccessful();

        $currency->refresh();

        $this->assertDatabaseCount('kimia_currencies', 1);
        $this->assertTrue(
            $currency->updated_at->equalTo($originalUpdatedAt)
        );
        $this->assertTrue(
            $currency->synced_at->equalTo($originalSyncedAt)
        );
    }
}
