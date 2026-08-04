<?php

namespace Tests\Feature\Trading;

use App\Models\KimiaCoin;
use App\Models\KimiaCurrency;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DynamicAssetOrderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_coin_and_currency_orders_from_visible_kimia_catalog_rows(): void
    {
        $user = User::factory()->create();
        KimiaCoin::query()->create(['kimia_id' => 16, 'name' => 'Imami', 'is_visible' => true]);
        KimiaCurrency::query()->create(['kimia_id' => 12, 'name' => 'USD', 'is_visible' => true]);

        $coin = app(OrderService::class)->create([
            'user_id' => $user->id, 'type' => 'buy', 'asset_type' => 'coin',
            'external_asset_id' => 16, 'asset_quantity' => '2', 'unit_price' => '187000000',
        ]);
        $currency = app(OrderService::class)->create([
            'user_id' => $user->id, 'type' => 'sell', 'asset_type' => 'currency',
            'external_asset_id' => 12, 'asset_quantity' => '100', 'unit_price' => '90000',
        ]);

        $this->assertSame(16, $coin->external_asset_id);
        $this->assertSame('coin', $coin->asset_type->value);
        $this->assertSame(12, $currency->external_asset_id);
        $this->assertSame('currency', $currency->asset_type->value);
    }

    #[Test]
    public function it_rejects_unknown_or_hidden_dynamic_assets(): void
    {
        $user = User::factory()->create();
        KimiaCoin::query()->create(['kimia_id' => 99, 'name' => 'Hidden', 'is_visible' => false]);

        $this->expectException(LogicException::class);
        app(OrderService::class)->create([
            'user_id' => $user->id, 'type' => 'buy', 'asset_type' => 'coin',
            'external_asset_id' => 99, 'asset_quantity' => '1', 'unit_price' => '1',
        ]);
    }
}
