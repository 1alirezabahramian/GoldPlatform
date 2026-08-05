<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFactorySchemaContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_creates_a_user_against_the_final_mobile_first_schema(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'mobile' => $user->mobile,
            'mobile_verified' => 1,
            'is_active' => 1,
        ]);

        $this->assertMatchesRegularExpression('/^09\d{9}$/', $user->mobile);
    }

    public function test_unverified_state_targets_mobile_verification(): void
    {
        $user = User::factory()->unverified()->create();

        $this->assertFalse($user->mobile_verified);
    }
}
