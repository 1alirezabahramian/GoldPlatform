<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AdminOperatorPermissionCatalog;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminSystemHealthReadContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_permission_can_read_safe_system_health(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/system/health');

        $response->assertOk()
            ->assertJsonPath('meta.api_version', 'v1')
            ->assertJsonStructure([
                'data' => [
                    'overall_status',
                    'components' => ['database', 'redis', 'cache', 'queue', 'outbox', 'storage', 'docker'],
                    'runtime' => ['environment', 'debug', 'php_version', 'laravel_version'],
                ],
                'meta',
                'message',
            ]);

        $payload = json_encode($response->json(), JSON_THROW_ON_ERROR);
        $this->assertStringNotContainsString('password', strtolower($payload));
        $this->assertStringNotContainsString('host', strtolower($payload));
    }

    public function test_operator_cannot_read_admin_system_health(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $operator = User::factory()->create();
        $operator->assignRole('operator');

        $this->actingAs($operator)
            ->getJson('/api/v1/admin/system/health')
            ->assertForbidden();
    }

    public function test_admin_role_without_health_permission_is_forbidden(): void
    {
        $this->seed(AdminOperatorPermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $admin->getRoleNames();
        $admin->getRoleNames();
        $admin->roles->first()->revokePermissionTo(AdminOperatorPermissionCatalog::SYSTEM_HEALTH_VIEW);

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/system/health')
            ->assertForbidden();
    }
}
