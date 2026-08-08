<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\TenantDomain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitPolicyTest extends TestCase
{
    use RefreshDatabase;

    private const HOST = 'rate-limit.test';

    public function test_auth_endpoints_are_rate_limited_by_ip(): void
    {
        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();

        TenantDomain::query()->firstOrCreate(
            ['host' => self::HOST],
            [
                'tenant_id' => $tenant->id,
                'is_primary' => true,
                'is_active' => true,
                'verified_at' => now(),
            ]
        );

        $url = 'https://'.self::HOST.'/api/auth/send-otp';

        for ($attempt = 1; $attempt <= 10; $attempt++) {
            $this->postJson($url, [])->assertStatus(422);
        }

        $this->postJson($url, [])
            ->assertStatus(429)
            ->assertHeader('Retry-After');
    }
}
