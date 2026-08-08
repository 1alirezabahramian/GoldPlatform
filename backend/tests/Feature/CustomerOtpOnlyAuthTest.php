<?php

namespace Tests\Feature;

use App\Models\Otp;
use App\Models\User;
use App\Services\Sms\DTO\SmsResult;
use App\Services\Sms\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CustomerOtpOnlyAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_mobile_is_not_sent_an_otp(): void
    {
        $this->postJson('/api/auth/send-otp', [
            'mobile' => '09120000111',
        ])->assertStatus(404)
            ->assertJsonPath('errors.code', 'CUSTOMER_NOT_REGISTERED');

        $this->assertDatabaseMissing('otps', [
            'mobile' => '09120000111',
        ]);
    }

    public function test_existing_customer_receives_login_otp(): void
    {
        User::factory()->create([
            'mobile' => '09120000112',
        ]);

        $sms = Mockery::mock(SmsService::class);
        $sms->shouldReceive('sendOtp')
            ->once()
            ->withArgs(fn (string $mobile, string $code) => $mobile === '09120000112' && strlen($code) === 6)
            ->andReturn(new SmsResult(true, 'ok'));
        $this->app->instance(SmsService::class, $sms);

        $this->postJson('/api/auth/send-otp', [
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
        $user = User::factory()->create([
            'mobile' => '09120000113',
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

        $response = $this->postJson('/api/auth/verify-otp', [
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
        $user = User::factory()->create([
            'mobile' => '09120000114',
        ]);

        Otp::query()->create([
            'mobile' => $user->mobile,
            'otp' => '123456',
            'purpose' => 'login',
            'attempts' => 0,
            'verified' => false,
            'expires_at' => now()->addMinutes(2),
        ]);

        $this->postJson('/api/auth/verify-otp', [
            'mobile' => $user->mobile,
            'otp' => '654321',
        ])->assertStatus(422)
            ->assertJsonPath('errors.code', 'OTP_INVALID_OR_EXPIRED');

        $this->assertDatabaseCount('personal_access_tokens', 0);
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
