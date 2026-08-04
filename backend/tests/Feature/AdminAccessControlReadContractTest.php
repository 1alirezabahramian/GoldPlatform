<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAccessControlReadContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminOperatorPermissionSeeder::class);
    }

    public function test_admin_can_read_roles_permissions_and_matrix(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/roles')
            ->assertOk()
            ->assertJsonStructure(['data' => ['items'], 'meta', 'message']);

        $this->getJson('/api/v1/admin/permissions')
            ->assertOk()
            ->assertJsonStructure(['data' => ['items'], 'meta', 'message']);

        $this->getJson('/api/v1/admin/access-matrix')
            ->assertOk()
            ->assertJsonStructure(['data' => ['roles', 'permissions', 'matrix'], 'meta', 'message']);
    }

    public function test_operator_cannot_read_access_control_endpoints(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $this->getJson('/api/v1/admin/roles')->assertForbidden();
        $this->getJson('/api/v1/admin/permissions')->assertForbidden();
        $this->getJson('/api/v1/admin/access-matrix')->assertForbidden();
    }

    public function test_access_control_requires_explicit_permission(): void
    {
        $user = User::factory()->create();
        Role::findOrCreate('limited-admin')->givePermissionTo('admin.access');
        $user->assignRole('limited-admin');
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/admin/roles')->assertForbidden();
    }

    public function test_role_output_does_not_expose_user_identity(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $encoded = json_encode(
            $this->getJson('/api/v1/admin/roles')->assertOk()->json(),
            JSON_THROW_ON_ERROR
        );

        $this->assertStringNotContainsString('mobile', $encoded);
        $this->assertStringNotContainsString('national_code', $encoded);
        $this->assertStringNotContainsString('email', $encoded);
    }
}
