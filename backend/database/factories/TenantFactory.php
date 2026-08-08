<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Support\IdentityOnboardingPolicy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('####'),
            'is_active' => true,
            'customer_auth_mode' => IdentityOnboardingPolicy::CUSTOMER_AUTH_OTP,
            'staff_auth_mode' => IdentityOnboardingPolicy::STAFF_AUTH_PASSWORD,
            'customer_registration_mode' => IdentityOnboardingPolicy::REGISTRATION_MANUAL,
        ];
    }
}
