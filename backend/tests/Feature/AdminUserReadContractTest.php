<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserGroup;
use Database\Seeders\AdminOperatorPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminUserReadContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminOperatorPermissionSeeder::class);
    }

    public function test_admin_can_read_safe_paginated_users(): void
    {
        $group = UserGroup::query()->create([
            'title' => 'ویژه',
            'is_active' => true,
        ]);

        $customer = User::factory()->create([
            'name' => 'Customer One',
            'mobile' => '09123456789',
            'national_code' => '1234567890',
            'group_id' => $group->id,
            'is_active' => true,
            'mobile_verified' => true,
        ]);
        Role::findOrCreate('customer')->givePermissionTo([]);
        $customer->assignRole('customer');

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/users?status=active&per_page=10')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'items' => [[
                        'id', 'display_name', 'mobile_masked', 'is_active',
                        'mobile_verified', 'last_login_at', 'created_at', 'group', 'roles',
                    ]],
                    'pagination' => ['current_page', 'per_page', 'total', 'last_page'],
                ],
                'meta' => ['request_id', 'generated_at', 'api_version'],
                'message',
            ]);

        $encoded = json_encode($response->json(), JSON_THROW_ON_ERROR);
        $this->assertStringContainsString('091*****789', $encoded);
        $this->assertStringNotContainsString('09123456789', $encoded);
        $this->assertStringNotContainsString('1234567890', $encoded);
        $this->assertStringNotContainsString('national_code', $encoded);
        $this->assertStringNotContainsString('account_id', $encoded);
        $this->assertStringNotContainsString('password', $encoded);
    }

    public function test_operator_cannot_read_admin_users(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('operator');
        Sanctum::actingAs($operator);

        $this->getJson('/api/v1/admin/users')->assertForbidden();
    }

    public function test_users_route_requires_explicit_permission(): void
    {
        $user = User::factory()->create();
        $limitedRole = Role::findOrCreate('limited-admin');
        $limitedRole->givePermissionTo('admin.access');
        $user->assignRole($limitedRole);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/admin/users')->assertForbidden();
    }

    public function test_invalid_user_filter_is_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/users?status=blocked')->assertUnprocessable();
        $this->getJson('/api/v1/admin/users?per_page=100')->assertUnprocessable();
    }
}
