<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_endpoints_are_rate_limited_by_ip(): void
    {
        for ($attempt = 1; $attempt <= 10; $attempt++) {
            $this->postJson('/api/auth/send-otp', [])->assertStatus(422);
        }

        $this->postJson('/api/auth/send-otp', [])
            ->assertStatus(429)
            ->assertHeader('Retry-After');
    }
}
