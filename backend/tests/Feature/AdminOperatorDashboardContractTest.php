<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminOperatorDashboardContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminOperatorPermissionSeeder::class);
    }

    public function test_admin_dashboard_uses_versioned_safe_envelope(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/dashboard')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'orders' => ['total', 'actionable', 'failed'],
                    'deliveries' => ['total', 'actionable', 'ready'],
                    'operations' => ['failed_jobs', 'outbox_messages', 'latest_audit_at'],
                ],
                'meta' => ['request_id', 'generated_at', 'api_version'],
                'message',
            ])
            ->assertJsonPath('meta.api_version', 'v1')
            ->assertJsonMissingPath('data.users')
            ->assertJsonMissingPath('data.kimia_credentials');
    }

    public function test_operator_dashboard_exposes_tasks_without_customer_identity(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $response = $this->getJson('/api/v1/operator/dashboard')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['task_counts', 'recent_orders', 'recent_deliveries'],
                'meta' => ['request_id', 'generated_at', 'api_version'],
                'message',
            ]);

        $payload = $response->json();
        $encoded = json_encode($payload, JSON_THROW_ON_ERROR);

        $this->assertStringNotContainsString('national_code', $encoded);
        $this->assertStringNotContainsString('receiver_identifier', $encoded);
        $this->assertStringNotContainsString('metadata', $encoded);
    }

    public function test_operator_cannot_access_admin_dashboard(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $this->getJson('/api/v1/admin/dashboard')->assertForbidden();
    }

    public function test_dashboard_requires_explicit_permission(): void
    {
        $user = User::factory()->create();
        Role::findOrCreate('limited-admin')->givePermissionTo('admin.access');
        $user->assignRole('limited-admin');
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/admin/dashboard')->assertForbidden();
    }
}
