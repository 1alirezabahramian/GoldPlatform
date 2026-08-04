<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class BackofficeSessionBootstrapContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bootstrap_returns_real_session_roles_and_permissions(): void
    {
        $admin = User::factory()->create([
            'first_name' => 'مدیر',
            'last_name' => 'آزمایشی',
            'mobile' => '09121234567',
            'is_active' => true,
        ]);

        $role = Role::findOrCreate('admin', 'web');
        $allowed = Permission::findOrCreate('audit-logs.view', 'web');
        Permission::findOrCreate('outbox.view', 'web');

        $role->givePermissionTo($allowed);
        $admin->assignRole($role);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/bootstrap')
            ->assertOk()
            ->assertJsonPath('data.panel', 'admin')
            ->assertJsonPath('data.session.authenticated', true)
            ->assertJsonPath('data.session.user.display_name', 'مدیر آزمایشی')
            ->assertJsonPath('data.session.user.mobile_masked', '0912***4567')
            ->assertJsonPath('data.session.roles.0', 'admin')
            ->assertJsonPath('data.session.permissions.0', 'audit-logs.view')
            ->assertJsonCount(1, 'data.navigation')
            ->assertJsonPath('data.navigation.0.code', 'audit_logs')
            ->assertJsonMissing(['mobile' => '09121234567'])
            ->assertJsonMissingPath('data.session.user.national_code');
    }

    public function test_operator_navigation_is_filtered_by_effective_permissions(): void
    {
        $operator = User::factory()->create(['is_active' => true]);

        $role = Role::findOrCreate('operator', 'web');
        $allowed = Permission::findOrCreate('deliveries.queue.view', 'web');
        Permission::findOrCreate('orders.queue.view', 'web');

        $role->givePermissionTo($allowed);
        $operator->assignRole($role);

        $this->actingAs($operator, 'sanctum')
            ->getJson('/api/v1/operator/bootstrap')
            ->assertOk()
            ->assertJsonCount(1, 'data.navigation')
            ->assertJsonPath('data.navigation.0.code', 'delivery_queue')
            ->assertJsonMissing(['code' => 'order_queue']);
    }

    public function test_unauthenticated_bootstrap_is_rejected(): void
    {
        $this->getJson('/api/v1/admin/bootstrap')->assertUnauthorized();
        $this->getJson('/api/v1/operator/bootstrap')->assertUnauthorized();
    }
}
