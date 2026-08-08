<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class CustomerAssetsReadContractTest extends TestCase
{
    use RefreshDatabase;

    private const HOST = 'v2-assets-contract.test';

    public function test_assets_require_authentication(): void
    {
        $this->getJson('/api/v1/customer/assets')->assertUnauthorized();
    }

    public function test_assets_fail_closed_instead_of_exposing_internal_financial_projection(): void
    {
        $customer = $this->tenantCustomerWithoutAccount();
        Sanctum::actingAs($customer);
        Http::fake();

        $response = $this->getJson('http://'.self::HOST.'/api/v1/customer/assets')
            ->assertServiceUnavailable()
            ->assertJsonPath('code', 'CUSTOMER_ACCOUNT_BINDING_REQUIRED')
            ->assertJsonMissingPath('data');

        Http::assertNothingSent();

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        foreach (['external_asset_id', 'asset_id', 'account_id', 'user_id', 'ledger_entries'] as $internalField) {
            $this->assertStringNotContainsString($internalField, $encoded);
        }
    }

    public function test_dynamic_asset_endpoints_do_not_fall_back_to_wallet_accounts(): void
    {
        $customer = $this->tenantCustomerWithoutAccount();
        Sanctum::actingAs($customer);
        Http::fake();

        foreach (['coins', 'currencies'] as $endpoint) {
            $this->getJson('http://'.self::HOST."/api/v1/customer/assets/{$endpoint}")
                ->assertServiceUnavailable()
                ->assertJsonPath('code', 'CUSTOMER_ACCOUNT_BINDING_REQUIRED')
                ->assertJsonMissingPath('data');
        }

        Http::assertNothingSent();
    }

    private function tenantCustomerWithoutAccount(): User
    {
        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();

        TenantDomain::query()->updateOrCreate(
            ['host' => self::HOST],
            [
                'tenant_id' => $tenant->id,
                'is_primary' => true,
                'is_active' => true,
                'verified_at' => now(),
            ],
        );

        Role::findOrCreate('customer', 'web');

        $customer = User::factory()->create([
            'tenant_id' => $tenant->id,
            'account_id' => null,
        ]);
        $customer->assignRole('customer');

        return $customer;
    }
}
