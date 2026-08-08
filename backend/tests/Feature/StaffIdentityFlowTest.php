<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffIdentityFlowTest extends TestCase
{
    use RefreshDatabase;

    private function tenantWithDomain(string $host): Tenant
    {
        $tenant = Tenant::factory()->create();

        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'host' => $host,
            'is_primary' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        return $tenant;
    }

    private function staff(Tenant $tenant, string $role = 'admin', string $username = 'admin'): User
    {
        Role::findOrCreate($role, 'web');

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'username' => $username,
            'password' => 'Initial-Password-123',
            'must_change_password' => true,
            'is_active' => true,
        ]);

        $user->assignRole($role);

        return $user;
    }

    public function test_staff_login_is_scoped_to_resolved_tenant_and_returns_first_login_flag(): void
    {
        $tenantA = $this->tenantWithDomain('admin.a.test');
        $tenantB = $this->tenantWithDomain('admin.b.test');

        $this->staff($tenantA, username: 'admin');
        $this->staff($tenantB, username: 'admin');

        $response = $this->postJson('https://admin.a.test/api/auth/staff/login', [
            'username' => 'admin',
            'password' => 'Initial-Password-123',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.user.username', 'admin')
            ->assertJsonPath('data.user.must_change_password', true);

        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_staff_cannot_login_through_another_tenant_domain(): void
    {
        $tenantA = $this->tenantWithDomain('admin.a.test');
        $this->tenantWithDomain('admin.b.test');
        $this->staff($tenantA, username: 'only-a');

        $this->postJson('https://admin.b.test/api/auth/staff/login', [
            'username' => 'only-a',
            'password' => 'Initial-Password-123',
        ])->assertUnprocessable();
    }

    public function test_staff_can_change_initial_password_and_clear_rotation_flag(): void
    {
        $tenant = $this->tenantWithDomain('admin.a.test');
        $user = $this->staff($tenant);
        Sanctum::actingAs($user);

        $this->postJson('https://admin.a.test/api/auth/staff/change-password', [
            'current_password' => 'Initial-Password-123',
            'password' => 'Replacement-Password-456',
            'password_confirmation' => 'Replacement-Password-456',
        ])->assertOk()->assertJsonPath('data.must_change_password', false);

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertNotNull($user->password_changed_at);
        $this->assertTrue(Hash::check('Replacement-Password-456', $user->password));
    }

    public function test_admin_settings_expose_confirmed_modes_and_fail_closed_for_automation(): void
    {
        $tenant = $this->tenantWithDomain('admin.a.test');
        $admin = $this->staff($tenant);
        Sanctum::actingAs($admin);

        $this->getJson('https://admin.a.test/api/admin/settings/identity-onboarding')
            ->assertOk()
            ->assertJsonPath('data.customer_auth_mode', 'otp')
            ->assertJsonPath('data.staff_auth_mode', 'password')
            ->assertJsonPath('data.customer_registration_mode', 'manual')
            ->assertJsonPath('data.readiness.jibit', false)
            ->assertJsonPath('data.readiness.kimia_customer_create', false);

        $this->putJson('https://admin.a.test/api/admin/settings/identity-onboarding', [
            'customer_registration_mode' => 'automatic',
        ])
            ->assertStatus(409)
            ->assertJsonPath('errors.code', 'REGISTRATION_MODE_DEPENDENCY_NOT_READY');

        $this->assertSame('manual', $tenant->fresh()->customer_registration_mode);
    }
}
