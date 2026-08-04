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

    public function test_customer_dashboard_has_stable_envelope_and_hides_internal_identifiers(): void
    {
        $customer = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $customer->assignRole('customer');
        Sanctum::actingAs($customer);

        $response = $this->getJson('/api/v1/customer/dashboard')
            ->assertOk()
            ->assertHeader('X-Request-Id')
            ->assertJsonStructure([
                'data' => [
                    'assets',
                    'summary' => [
                        'active_orders',
                        'custodies',
                        'delivery_requests',
                        'ready_deliveries',
                    ],
                ],
                'meta' => ['request_id', 'generated_at', 'api_version'],
                'message',
            ])
            ->assertJsonPath('meta.api_version', 'v1')
            ->assertJsonPath('message', null);

        $payload = $response->json();
        $encoded = json_encode($payload, JSON_THROW_ON_ERROR);

        $this->assertStringNotContainsString('external_asset_id', $encoded);
        $this->assertStringNotContainsString('asset_id', $encoded);
        $this->assertStringNotContainsString('user_id', $encoded);
        $this->assertStringNotContainsString('account_id', $encoded);
    }

    public function test_dashboard_only_counts_records_owned_by_authenticated_customer(): void
    {
        $customer = User::factory()->create();
        $other = User::factory()->create();
        Role::findOrCreate('customer', 'web');
        $customer->assignRole('customer');
        $other->assignRole('customer');
        Sanctum::actingAs($customer);

        $this->getJson('/api/v1/customer/dashboard')
            ->assertOk()
            ->assertJsonPath('data.summary.active_orders', 0)
            ->assertJsonPath('data.summary.custodies', 0)
            ->assertJsonPath('data.summary.delivery_requests', 0)
            ->assertJsonPath('data.summary.ready_deliveries', 0);
    }
}
