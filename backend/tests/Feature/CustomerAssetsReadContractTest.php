<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class CustomerAssetsReadContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_assets_require_authentication(): void
    {
        $this->getJson('/api/v1/customer/assets')->assertUnauthorized();
    }

    public function test_assets_fail_closed_instead_of_exposing_internal_financial_projection(): void
    {
        $customer = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $customer->assignRole('customer');
        Sanctum::actingAs($customer);

        $response = $this->getJson('/api/v1/customer/assets')
            ->assertServiceUnavailable()
            ->assertJsonPath('code', 'KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED')
            ->assertJsonMissingPath('data');

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        foreach (['external_asset_id', 'asset_id', 'account_id', 'user_id', 'ledger_entries'] as $internalField) {
            $this->assertStringNotContainsString($internalField, $encoded);
        }
    }

    public function test_dynamic_asset_endpoints_do_not_fall_back_to_wallet_accounts(): void
    {
        $customer = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $customer->assignRole('customer');
        Sanctum::actingAs($customer);

        foreach (['coins', 'currencies'] as $endpoint) {
            $this->getJson("/api/v1/customer/assets/{$endpoint}")
                ->assertServiceUnavailable()
                ->assertJsonPath('code', 'KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED')
                ->assertJsonMissingPath('data');
        }
    }
}
