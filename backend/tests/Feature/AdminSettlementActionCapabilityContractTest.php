<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminSettlementActionCapabilityContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_read_blocked_sensitive_action_capabilities(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/settlement-actions/overview');

        $response->assertOk()
            ->assertJsonPath('data.actions.retry.supported', false)
            ->assertJsonPath('data.actions.kimia_write.supported', false)
            ->assertJsonPath('data.discovery.write_endpoints_exposed', false)
            ->assertJsonPath('meta.api_version', 'v1');
    }

    public function test_operator_cannot_read_admin_settlement_action_capabilities(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $operator = User::factory()->create();
        $operator->assignRole('operator');

        $this->actingAs($operator)
            ->getJson('/api/v1/admin/settlement-actions/overview')
            ->assertForbidden();
    }

    public function test_retry_endpoint_is_not_exposed(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->postJson('/api/v1/admin/settlements/1/retry', [], ['Idempotency-Key' => 'test'])
            ->assertNotFound();
    }
}
