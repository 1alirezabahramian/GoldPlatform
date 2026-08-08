<?php

namespace Tests\Feature;

use App\Models\Otp;
use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use App\Services\Sms\DTO\SmsResult;
use App\Services\Sms\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CustomerOtpOnlyAuthTest extends TestCase
{
    use RefreshDatabase;

    private const HOST = 'customer-auth.test';

    private function tenant(): Tenant
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

        return $tenant;
    }

    private function authUrl(string $path): string
    {
        return 'https://'.self::HOST.'/api/auth/'.$path;
    }

    public function test_unknown_mobile_is_not_sent_an_otp(): void
    {
        $this->tenant();

        $this->postJson($this->authUrl('send-otp'), [
            'mobile' => '09120000111',
        ])->assertStatus(404)
            ->assertJsonPath('errors.code', 'CUSTOMER_NOT_REGISTERED');

        $this->assertDatabaseMissing('otps', [
            'mobile' => '09120000111',
        ]);
    }

    public function test_existing_customer_receives_login_otp(): void
    {
        $tenant = $this->tenant();
        User::factory()->create([
            'mobile' => '09120000112',
            'tenant_id' => $tenant->id,
        ]);

        $sms = Mockery::mock(SmsService::class);
        $sms->shouldReceive('sendOtp')
            ->once()
            ->withArgs(fn (string $mobile, string $code) => $mobile === '09120000112' && strlen($code) === 6)
            ->andReturn(new SmsResult(true, 'ok'));
        $this->app->instance(SmsService::class, $sms);

        $this->postJson($this->authUrl('send-otp'), [
            'mobile' => '09120000112',
        ])->assertOk();

        $this->assertDatabaseHas('otps', [
            'mobile' => '09120000112',
            'purpose' => 'login',
            'verified' => false,
        ]);
    }

    public function test_valid_otp_logs_existing_customer_in_without_password(): void
    {
        $tenant = $this->tenant();
        $user = User::factory()->create([
            'mobile' => '09120000113',
            'tenant_id' => $tenant->id,
            'mobile_verified' => true,
        ]);

        Otp::query()->create([
            'mobile' => $user->mobile,
            'otp' => '123456',
            'purpose' => 'login',
            'attempts' => 0,
            'verified' => false,
            'expires_at' => now()->addMinutes(2),
        ]);

        $response = $this->postJson($this->authUrl('verify-otp'), [
            'mobile' => $user->mobile,
            'otp' => '123456',
        ])->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer');

        $this->assertNotEmpty($response->json('data.token'));
        $this->assertDatabaseHas('otps', [
            'mobile' => $user->mobile,
            'verified' => true,
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_invalid_otp_does_not_issue_a_token(): void
    {
        $tenant = $this->tenant();
        $user = User::factory()->create([
            'mobile' => '09120000114',
            'tenant_id' => $tenant->id,
        ]);

        Otp::query()->create([
            'mobile' => $user->mobile,
            'otp' => '123456',
            'purpose' => 'login',
            'attempts' => 0,
            'verified' => false,
            'expires_at' => now()->addMinutes(2),
        ]);

        $this->postJson($this->authUrl('verify-otp'), [
            'mobile' => $user->mobile,
            'otp' => '654321',
        ])->assertStatus(422)
            ->assertJsonPath('errors.code', 'OTP_INVALID_OR_EXPIRED');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_customer_cannot_request_otp_through_another_tenant_domain(): void
    {
        $tenant = $this->tenant();
        User::factory()->create([
            'mobile' => '09120000116',
            'tenant_id' => $tenant->id,
        ]);

        $otherTenant = Tenant::query()->create([
            'name' => 'Other Tenant',
            'slug' => 'other-auth-tenant',
            'is_active' => true,
        ]);
        TenantDomain::query()->create([
            'tenant_id' => $otherTenant->id,
            'host' => 'other-customer-auth.test',
            'is_primary' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        $this->postJson('https://other-customer-auth.test/api/auth/send-otp', [
            'mobile' => '09120000116',
        ])->assertStatus(404)
            ->assertJsonPath('errors.code', 'CUSTOMER_NOT_REGISTERED');

        $this->assertDatabaseMissing('otps', [
            'mobile' => '09120000116',
        ]);
    }

    public function test_unknown_domain_fails_closed_before_customer_lookup(): void
    {
        $this->postJson('https://unknown-customer-auth.test/api/auth/send-otp', [
            'mobile' => '09120000117',
        ])->assertNotFound();

        $this->assertDatabaseMissing('otps', [
            'mobile' => '09120000117',
        ]);
    }

    public function test_logout_revokes_the_current_sanctum_token(): void
    {
        $user = User::factory()->create([
            'mobile' => '09120000115',
        ]);
        $plainTextToken = $user->createToken('customer-mobile')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$plainTextToken)
            ->postJson('/api/auth/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
