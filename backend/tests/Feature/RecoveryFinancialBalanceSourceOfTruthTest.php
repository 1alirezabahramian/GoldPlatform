<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class RecoveryFinancialBalanceSourceOfTruthTest extends TestCase
{
    use RefreshDatabase;

    private const HOST = 'v2-source-of-truth.test';

    public function test_customer_asset_endpoints_fail_closed_instead_of_exposing_internal_projection(): void
    {
        $customer = $this->tenantCustomerWithoutAccount();
        Http::fake();

        foreach ([
            '/api/v1/customer/assets',
            '/api/v1/customer/assets/money',
            '/api/v1/customer/assets/gold',
            '/api/v1/customer/assets/coins',
            '/api/v1/customer/assets/currencies',
        ] as $uri) {
            $this->actingAs($customer, 'sanctum')
                ->getJson('http://'.self::HOST.$uri)
                ->assertStatus(503)
                ->assertJsonPath('code', 'CUSTOMER_ACCOUNT_BINDING_REQUIRED')
                ->assertJsonMissingPath('data');
        }

        Http::assertNothingSent();
    }

    public function test_customer_dashboard_does_not_expose_ledger_derived_balances(): void
    {
        $customer = $this->customer();

        $this->actingAs($customer, 'sanctum')
            ->getJson('/api/v1/customer/dashboard')
            ->assertStatus(503)
            ->assertJsonPath('code', 'KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED')
            ->assertJsonMissingPath('data');
    }

    public function test_public_customer_balance_controllers_do_not_reference_wallet_or_projection_services(): void
    {
        $assets = (string) file_get_contents(
            app_path('Http/Controllers/Api/V1/CustomerAssetReadController.php')
        );
        $dashboard = (string) file_get_contents(
            app_path('Http/Controllers/Api/V1/CustomerDashboardController.php')
        );

        foreach (['wallet', 'ledgerEntries', 'BalanceProjectionService', 'CustomerBalancePresenter'] as $forbidden) {
            self::assertStringNotContainsString($forbidden, $assets.$dashboard);
        }
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

        return $this->customer([
            'tenant_id' => $tenant->id,
            'account_id' => null,
        ]);
    }

    private function customer(array $attributes = []): User
    {
        Role::findOrCreate('customer', 'web');

        $user = User::factory()->create($attributes);
        $user->assignRole('customer');

        return $user;
    }
}
