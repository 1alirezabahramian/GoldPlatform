<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminOperatorOperationalQueueContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminOperatorPermissionSeeder::class);
    }

    public function test_operator_order_queue_is_versioned_paginated_and_safe(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $response = $this->getJson('/api/v1/operator/orders/queue?per_page=10')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'items',
                    'pagination' => ['current_page', 'per_page', 'total', 'last_page'],
                ],
                'meta' => ['request_id', 'generated_at', 'api_version'],
                'message',
            ])
            ->assertJsonPath('data.pagination.per_page', 10)
            ->assertJsonPath('meta.api_version', 'v1');

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        $this->assertStringNotContainsString('national_code', $encoded);
        $this->assertStringNotContainsString('account_id', $encoded);
        $this->assertStringNotContainsString('metadata', $encoded);
    }

    public function test_operator_delivery_queue_is_versioned_and_safe(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $response = $this->getJson('/api/v1/operator/deliveries/queue')
            ->assertOk()
            ->assertJsonStructure(['data' => ['items', 'pagination'], 'meta', 'message']);

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        $this->assertStringNotContainsString('receiver_identifier', $encoded);
        $this->assertStringNotContainsString('receiver_name', $encoded);
        $this->assertStringNotContainsString('user_id', $encoded);
    }

    public function test_admin_audit_contract_excludes_sensitive_snapshots(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/audit-logs')
            ->assertOk()
            ->assertJsonStructure(['data' => ['items', 'pagination'], 'meta', 'message']);

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        $this->assertStringNotContainsString('before', $encoded);
        $this->assertStringNotContainsString('after', $encoded);
        $this->assertStringNotContainsString('metadata', $encoded);
        $this->assertStringNotContainsString('user_agent', $encoded);
    }

    public function test_admin_outbox_contract_excludes_payload(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/outbox')
            ->assertOk()
            ->assertJsonStructure(['data' => ['items', 'pagination'], 'meta', 'message']);

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        $this->assertStringNotContainsString('payload', $encoded);
        $this->assertStringNotContainsString('aggregate_id', $encoded);
    }

    public function test_operator_cannot_access_admin_operational_reads(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $this->getJson('/api/v1/admin/audit-logs')->assertForbidden();
        $this->getJson('/api/v1/admin/outbox')->assertForbidden();
    }

    public function test_unsupported_queue_status_filter_is_rejected(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $this->getJson('/api/v1/operator/orders/queue?status=completed')
            ->assertUnprocessable();
    }
}
