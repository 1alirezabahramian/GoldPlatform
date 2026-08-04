<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AdminOperatorPermissionCatalog;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminOperatorPermissionFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminOperatorPermissionSeeder::class);
    }

    public function test_operator_role_receives_only_verified_operator_permissions(): void
    {
        $role = Role::findByName('operator');

        $this->assertTrue($role->hasPermissionTo(AdminOperatorPermissionCatalog::OPERATOR_ACCESS));
        $this->assertTrue($role->hasPermissionTo(AdminOperatorPermissionCatalog::DELIVERIES_COMPLETE));
        $this->assertFalse($role->hasPermissionTo(AdminOperatorPermissionCatalog::AUDIT_VIEW));
        $this->assertFalse($role->hasPermissionTo(AdminOperatorPermissionCatalog::CUSTOMER_POLICIES_UPDATE));
    }

    public function test_operator_with_permission_can_view_order_queue(): void
    {
        $user = User::factory()->create();
        $user->assignRole('operator');
        Sanctum::actingAs($user);

        $this->getJson('/api/operator/orders/queue')
            ->assertOk();
    }

    public function test_operator_cannot_access_admin_audit_route(): void
    {
        $user = User::factory()->create();
        $user->assignRole('operator');
        Sanctum::actingAs($user);

        $this->getJson('/api/admin/audit-logs')
            ->assertForbidden();
    }

    public function test_role_without_required_permission_is_denied(): void
    {
        $user = User::factory()->create();
        $user->assignRole('operator');
        $user->revokePermissionTo(AdminOperatorPermissionCatalog::ORDERS_QUEUE_VIEW);
        $user->getRoleNames()->each(fn (string $roleName) => $user->removeRole($roleName));

        Role::findOrCreate('limited-operator')->givePermissionTo(AdminOperatorPermissionCatalog::OPERATOR_ACCESS);
        $user->assignRole('limited-operator');
        Sanctum::actingAs($user);

        $this->getJson('/api/operator/orders/queue')
            ->assertForbidden();
    }
}
