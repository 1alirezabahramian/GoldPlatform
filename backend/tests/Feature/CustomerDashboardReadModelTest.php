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

    public function test_dashboard_exposes_operational_sections_without_internal_identifiers(): void
    {
        $customer = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $customer->assignRole('customer');
        Sanctum::actingAs($customer);

        $response = $this->getJson('/api/v1/customer/dashboard')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'assets',
                    'summary' => [
                        'active_orders',
                        'custodies',
                        'delivery_requests',
                        'ready_deliveries',
                    ],
                    'highlights' => [
                        'active_orders',
                        'ready_deliveries',
                    ],
                    'recent_activity',
                ],
                'meta' => ['request_id', 'generated_at', 'api_version'],
                'message',
            ]);

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);

        foreach (['external_asset_id', 'external_product_id', 'user_id', 'account_id', 'metadata', 'receiver_identifier'] as $internalField) {
            $this->assertStringNotContainsString($internalField, $encoded);
        }
    }

    public function test_dashboard_query_count_does_not_grow_with_multiple_asset_accounts(): void
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
            'Customer dashboard exceeded its fixed ten-query read budget.',
        );
    }
}
