<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CustomerDashboardReadModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_internal_dashboard_read_model_contains_only_goldplatform_owned_operational_data(): void
    {
        $customer = User::factory()->create();

        $dashboard = app(CustomerDashboardReadModel::class)->for($customer);

        $this->assertArrayNotHasKey('assets', $dashboard);
        $this->assertArrayHasKey('summary', $dashboard);
        $this->assertArrayHasKey('highlights', $dashboard);
        $this->assertArrayHasKey('recent_activity', $dashboard);

        $source = file_get_contents(app_path('Services/CustomerDashboardReadModel.php'));
        $this->assertIsString($source);

        foreach (['CustomerBalancePresenter', 'ledgerEntries', 'balanceReservations', "->wallet"] as $forbiddenDependency) {
            $this->assertStringNotContainsString($forbiddenDependency, $source);
        }
    }
}
