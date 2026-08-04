<?php

namespace Tests\Feature;

use App\Models\KimiaCurrency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncKimiaCurrenciesCommandTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_it_creates_and_updates_kimia_currencies_without_duplicates(): void
    {
        Http::fakeSequence()
            ->push([
                ['CurrencyId' => 11, 'Name' => 'Rial', 'IsVisible' => true],
                ['CurrencyId' => 12, 'Name' => 'US Dollar', 'IsVisible' => true],
            ])
            ->push([
                ['CurrencyId' => 11, 'Name' => 'Iranian Rial', 'IsVisible' => false],
                ['CurrencyId' => 12, 'Name' => 'US Dollar', 'IsVisible' => true],
            ]);

        $this->artisan('kimia:sync-currencies')->assertSuccessful();
        $this->assertDatabaseCount('kimia_currencies', 2);
        $this->artisan('kimia:sync-currencies')->assertSuccessful();
        $this->assertDatabaseCount('kimia_currencies', 2);

        $currency = KimiaCurrency::query()->where('kimia_id', 11)->firstOrFail();
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

        Http::fake([ 'https://kimia.test/*' => Http::response([[
            'CurrencyId' => 12,
            'Name' => 'US Dollar',
            'IsVisible' => true,
        ]]) ]);

        $this->artisan('kimia:sync-currencies')->assertSuccessful();
        $currency->refresh();
        $this->assertDatabaseCount('kimia_currencies', 1);
        $this->assertTrue($currency->updated_at->equalTo($originalUpdatedAt));
        $this->assertTrue($currency->synced_at->equalTo($originalSyncedAt));
    }
}
