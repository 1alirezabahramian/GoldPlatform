<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Support\IdentityOnboardingPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdentityOnboardingFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_defaults_preserve_confirmed_identity_policy(): void
    {
        $tenant = Tenant::factory()->create();

        $this->assertSame(IdentityOnboardingPolicy::CUSTOMER_AUTH_OTP, $tenant->customer_auth_mode);
        $this->assertSame(IdentityOnboardingPolicy::STAFF_AUTH_PASSWORD, $tenant->staff_auth_mode);
        $this->assertSame(IdentityOnboardingPolicy::REGISTRATION_MANUAL, $tenant->customer_registration_mode);
    }

    public function test_assisted_and_automatic_registration_fail_closed_until_jibit_and_kimia_create_are_ready(): void
    {
        $this->assertFalse(IdentityOnboardingPolicy::canActivateRegistrationMode('manual', false, false));
        $this->assertTrue(IdentityOnboardingPolicy::canActivateRegistrationMode('manual', true, false));

        $this->assertFalse(IdentityOnboardingPolicy::canActivateRegistrationMode('assisted', true, false));
        $this->assertFalse(IdentityOnboardingPolicy::canActivateRegistrationMode('automatic', true, false));
        $this->assertTrue(IdentityOnboardingPolicy::canActivateRegistrationMode('assisted', true, true));
        $this->assertTrue(IdentityOnboardingPolicy::canActivateRegistrationMode('automatic', true, true));
    }

    public function test_referrer_relation_is_stored_by_user_id_within_the_same_tenant_fixture(): void
    {
        $tenant = Tenant::factory()->create();

        $referrer = User::factory()->create([
            'tenant_id' => $tenant->id,
            'mobile' => '09175650080',
            'referral_code' => 'AL7K92',
        ]);

        $referred = User::factory()->create([
            'tenant_id' => $tenant->id,
            'mobile' => '09120000199',
            'referrer_user_id' => $referrer->id,
        ]);

        $this->assertTrue($referred->referrer->is($referrer));
        $this->assertTrue($referrer->referrals->contains($referred));
    }

    public function test_staff_first_login_flag_is_explicit_and_password_is_not_hardcoded_by_the_foundation(): void
    {
        $tenant = Tenant::factory()->create();

        $staff = User::factory()->create([
            'tenant_id' => $tenant->id,
            'username' => 'admin',
            'must_change_password' => true,
        ]);

        $this->assertTrue($staff->must_change_password);
        $this->assertNull($staff->password_changed_at);
    }
}
