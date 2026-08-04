<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WalletAccount;
use App\Services\Wallet\BalanceProjectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Mockery\MockInterface;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class CustomerAssetsReadContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_assets_require_authentication(): void
    {
        $this->getJson('/api/v1/customer/assets')->assertUnauthorized();
    }

    public function test_assets_are_dynamic_precise_and_do_not_expose_internal_identifiers(): void
    {
        $customer = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $customer->assignRole('customer');
        Sanctum::actingAs($customer);
        $customer->load('wallet');

        $customer->wallet->accounts()->delete();

        foreach ([
            ['code' => 'money:main', 'title' => 'پول من', 'asset_type' => 'money', 'unit' => 'IRR'],
            ['code' => 'gold:18k', 'title' => 'طلای من', 'asset_type' => 'gold', 'unit' => 'gram'],
            ['code' => 'coin:dynamic-a', 'title' => 'سکه پویا', 'asset_type' => 'coin', 'unit' => 'piece', 'external_asset_id' => 991],
            ['code' => 'currency:dynamic-a', 'title' => 'ارز پویا', 'asset_type' => 'currency', 'unit' => 'unit', 'external_asset_id' => 992],
        ] as $account) {
            WalletAccount::query()->create($account + [
                'wallet_id' => $customer->wallet->id,
                'balance' => '0',
                'blocked_balance' => '0',
                'is_active' => true,
            ]);
        }

        $this->mock(BalanceProjectionService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('snapshot')->andReturnUsing(
                fn (WalletAccount $account) => match ($account->asset_type->value) {
                    'money' => ['total' => '-12500000', 'blocked' => '0', 'available' => '-12500000'],
                    'gold' => ['total' => '1.25000000', 'blocked' => '0.25000000', 'available' => '1.00000000'],
                    default => ['total' => '2.00000000', 'blocked' => '0', 'available' => '2.00000000'],
                },
            );
        });

        $response = $this->getJson('/api/v1/customer/assets')
            ->assertOk()
            ->assertJsonPath('meta.count', 4)
            ->assertJsonPath('data.items.0.type', 'coin')
            ->assertJsonPath('data.items.1.type', 'currency')
            ->assertJsonPath('data.items.2.type', 'gold')
            ->assertJsonPath('data.items.3.type', 'money')
            ->assertJsonPath('data.items.3.balance.total', '-12500000')
            ->assertJsonPath('data.items.2.balance.available', '1.00000000');

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        foreach (['external_asset_id', 'asset_id', 'account_id', 'user_id', 'ledger_entries'] as $internalField) {
            $this->assertStringNotContainsString($internalField, $encoded);
        }
    }

    public function test_coin_and_currency_endpoints_filter_dynamic_accounts(): void
    {
        $customer = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $customer->assignRole('customer');
        Sanctum::actingAs($customer);
        $customer->load('wallet');
        $customer->wallet->accounts()->delete();

        foreach ([
            ['code' => 'coin:first', 'title' => 'First Coin', 'asset_type' => 'coin', 'unit' => 'piece'],
            ['code' => 'coin:second', 'title' => 'Second Coin', 'asset_type' => 'coin', 'unit' => 'piece'],
            ['code' => 'currency:first', 'title' => 'First Currency', 'asset_type' => 'currency', 'unit' => 'unit'],
        ] as $account) {
            WalletAccount::query()->create($account + [
                'wallet_id' => $customer->wallet->id,
                'balance' => '0',
                'blocked_balance' => '0',
                'is_active' => true,
            ]);
        }

        $this->mock(BalanceProjectionService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('snapshot')->andReturn([
                'total' => '0',
                'blocked' => '0',
                'available' => '0',
            ]);
        });

        $this->getJson('/api/v1/customer/assets/coins')
            ->assertOk()
            ->assertJsonPath('meta.asset_type', 'coin')
            ->assertJsonCount(2, 'data.items');

        $this->getJson('/api/v1/customer/assets/currencies')
            ->assertOk()
            ->assertJsonPath('meta.asset_type', 'currency')
            ->assertJsonCount(1, 'data.items');
    }
}
