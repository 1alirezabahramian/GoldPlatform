<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WalletAccount;
use App\Services\CustomerDashboardReadModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class CustomerDashboardReadModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_dashboard_does_not_expose_internal_financial_projection(): void
    {
        $customer = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $customer->assignRole('customer');
        Sanctum::actingAs($customer);

        $response = $this->getJson('/api/v1/customer/dashboard')
            ->assertServiceUnavailable()
            ->assertJsonPath('code', 'KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED')
            ->assertJsonMissingPath('data');

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);

        foreach (['external_asset_id', 'external_product_id', 'user_id', 'account_id', 'metadata', 'receiver_identifier'] as $internalField) {
            $this->assertStringNotContainsString($internalField, $encoded);
        }
    }

    public function test_internal_dashboard_projection_query_count_does_not_grow_with_multiple_asset_accounts(): void
    {
        $customer = User::factory()->create();
        $customer->load('wallet');
        $this->assertNotNull($customer->wallet);

        foreach ([
            ['code' => 'cp03:money', 'title' => 'Money', 'asset_type' => 'money', 'unit' => 'IRR'],
            ['code' => 'cp03:gold', 'title' => 'Gold', 'asset_type' => 'gold', 'unit' => 'gram'],
            ['code' => 'cp03:coin', 'title' => 'Coin', 'asset_type' => 'coin', 'unit' => 'piece'],
            ['code' => 'cp03:currency', 'title' => 'Currency', 'asset_type' => 'currency', 'unit' => 'unit'],
        ] as $account) {
            WalletAccount::query()->create($account + [
                'wallet_id' => $customer->wallet->id,
                'balance' => '0',
                'blocked_balance' => '0',
                'is_active' => true,
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        app(CustomerDashboardReadModel::class)->for($customer);

        $this->assertLessThanOrEqual(
            10,
            count(DB::getQueryLog()),
            'Internal dashboard projection exceeded its fixed ten-query read budget.',
        );
    }
}
