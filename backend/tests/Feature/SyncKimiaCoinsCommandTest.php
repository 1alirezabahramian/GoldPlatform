<?php

namespace Tests\Feature;

use App\Models\KimiaCoin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncKimiaCoinsCommandTest extends TestCase
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

    public function test_it_creates_and_updates_kimia_coins_without_duplicates(): void
    {
        Http::fakeSequence()
            ->push([
                ['CoinId' => 101, 'Name' => 'Imami Coin', 'Fineness' => 900, 'Weight' => 8.133, 'Type' => 15, 'IsVisible' => true],
                ['CoinId' => 102, 'Name' => 'Half Coin', 'Fineness' => 900, 'Weight' => 4.066, 'Type' => 15, 'IsVisible' => true],
            ])
            ->push([
                ['CoinId' => 101, 'Name' => 'Updated Imami Coin', 'Fineness' => 916, 'Weight' => 8.133, 'Type' => 15, 'IsVisible' => false],
                ['CoinId' => 102, 'Name' => 'Half Coin', 'Fineness' => 900, 'Weight' => 4.066, 'Type' => 15, 'IsVisible' => true],
            ]);

        $this->artisan('kimia:sync-coins')->assertSuccessful();
        $this->assertDatabaseCount('kimia_coins', 2);
        $this->artisan('kimia:sync-coins')->assertSuccessful();
        $this->assertDatabaseCount('kimia_coins', 2);

        $coin = KimiaCoin::query()->where('kimia_id', 101)->firstOrFail();
        $this->assertSame('Updated Imami Coin', $coin->name);
        $this->assertSame('916.0000', $coin->fineness);
        $this->assertFalse($coin->is_visible);
        $this->assertNotNull($coin->synced_at);
    }

    public function test_it_skips_unchanged_kimia_coins(): void
    {
        $coin = KimiaCoin::create([
            'kimia_id' => 202, 'name' => 'Unchanged Coin', 'fineness' => 900,
            'weight' => 8.133, 'type' => 15, 'is_visible' => true, 'synced_at' => now()->subDay(),
        ]);
        $originalUpdatedAt = $coin->updated_at;
        $originalSyncedAt = $coin->synced_at;

        Http::fake([ 'https://kimia.test/*' => Http::response([[
            'CoinId' => 202, 'Name' => 'Unchanged Coin', 'Fineness' => 900,
            'Weight' => 8.133, 'Type' => 15, 'IsVisible' => true,
        ]]) ]);

        $this->artisan('kimia:sync-coins')->assertSuccessful();
        $coin->refresh();
        $this->assertDatabaseCount('kimia_coins', 1);
        $this->assertTrue($coin->updated_at->equalTo($originalUpdatedAt));
        $this->assertTrue($coin->synced_at->equalTo($originalSyncedAt));
    }
}
