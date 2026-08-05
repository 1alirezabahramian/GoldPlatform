<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

final class RecoveryCustomerProfileHttpTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_read_safe_profile_envelope(): void
    {
        $customer = $this->userWithRole('customer');

        $this->actingAs($customer, 'sanctum')
            ->getJson('/api/v1/customer/profile')
            ->assertOk()
            ->assertJsonPath('data.profile.mobile', $customer->mobile)
            ->assertJsonPath('data.profile.is_active', (bool) $customer->is_active)
            ->assertJsonPath('meta.api_version', 'v1')
            ->assertJsonPath('message', null)
            ->assertJsonMissingPath('data.profile.id')
            ->assertJsonMissingPath('data.profile.account_id')
            ->assertJsonMissingPath('data.profile.group_id')
            ->assertJsonMissingPath('data.profile.password')
            ->assertJsonMissingPath('data.profile.remember_token')
            ->assertJsonMissingPath('data.profile.national_code');
    }

    public function test_non_customer_cannot_read_customer_profile(): void
    {
        $operator = $this->userWithRole('operator');

        $this->actingAs($operator, 'sanctum')
            ->getJson('/api/v1/customer/profile')
            ->assertForbidden();
    }

    public function test_unauthenticated_profile_request_is_rejected(): void
    {
        $this->getJson('/api/v1/customer/profile')
            ->assertUnauthorized();
    }

    private function userWithRole(string $roleName): User
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::findOrCreate($roleName, 'web');

        $user = User::factory()->create();
        $user->assignRole($roleName);

        return $user;
    }
}
