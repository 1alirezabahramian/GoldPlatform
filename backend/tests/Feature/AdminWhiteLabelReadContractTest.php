<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminWhiteLabelReadContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_read_white_label_capability_overview(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->getJson('/api/v1/admin/white-label/overview');

        $response->assertOk()
            ->assertJsonPath('meta.api_version', 'v1')
            ->assertJsonPath('data.capabilities.tenant_entity_supported', false)
            ->assertJsonPath('data.capabilities.custom_domain_supported', false)
            ->assertJsonPath('data.discovery.requires_architecture_decision', true);

        $payload = strtolower(json_encode($response->json(), JSON_THROW_ON_ERROR));
        $this->assertStringNotContainsString('password', $payload);
        $this->assertStringNotContainsString('secret', $payload);
    }

    public function test_operator_cannot_read_white_label_overview(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $operator = User::factory()->create();
        $operator->assignRole('operator');

        $this->actingAs($operator)
            ->getJson('/api/v1/admin/white-label/overview')
            ->assertForbidden();
    }
}
