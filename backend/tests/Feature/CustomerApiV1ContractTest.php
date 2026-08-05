<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class CustomerApiV1ContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_authentication(): void
    {
        $this->getJson('/api/v1/customer/dashboard')->assertUnauthorized();
    }

    public function test_non_customer_cannot_read_customer_dashboard(): void
    {
        $operator = User::factory()->create();
        Role::findOrCreate('operator', 'web');
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $this->getJson('/api/v1/customer/dashboard')->assertForbidden();
    }

    public function test_customer_dashboard_fails_closed_until_kimia_balance_resolution_exists(): void
    {
        $customer = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $customer->assignRole('customer');
        Sanctum::actingAs($customer);

        $response = $this->getJson('/api/v1/customer/dashboard')
            ->assertServiceUnavailable()
            ->assertHeader('X-Request-Id')
            ->assertJsonPath('code', 'KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED')
            ->assertJsonMissingPath('data');

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);

        foreach (['external_asset_id', 'asset_id', 'user_id', 'account_id', 'ledger_entries'] as $internalField) {
            $this->assertStringNotContainsString($internalField, $encoded);
        }
    }

    public function test_dashboard_does_not_fall_back_to_internal_records_for_another_customer(): void
    {
        $customer = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $customer->assignRole('customer');
        Sanctum::actingAs($customer);

        $this->getJson('/api/v1/customer/dashboard')
            ->assertServiceUnavailable()
            ->assertJsonPath('code', 'KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED')
            ->assertJsonMissingPath('data');
    }
}
