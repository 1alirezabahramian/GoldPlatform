<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(
        protected OtpService $otpService,
        protected SmsService $smsService
    ) {
    }

    public function sendOtp(string $mobile): array
    {
        $otp = $this->otpService->generate();

        Otp::updateOrCreate(
            [
                'mobile' => $mobile,
                'purpose' => 'login',
            ],
            [
                'otp' => $otp,
                'attempts' => 0,
                'verified' => false,
                'expires_at' => $this->otpService->expiresAt(),
                'verified_at' => null,
            ]
        );

        $this->smsService->sendOtp($mobile, $otp);

        return [
            'success' => true,
            'message' => 'OTP sent successfully.',
        ];
    }

    public function verifyOtp(string $mobile, string $code): array
    {
        $otp = Otp::where('mobile', $mobile)
            ->where('purpose', 'login')
            ->where('verified', false)
            ->latest()
            ->first();

        if (!$otp) {
            return [
                'success' => false,
                'message' => 'OTP not found.',
            ];
        }

        if (now()->greaterThan($otp->expires_at)) {
            return [
                'success' => false,
                'message' => 'OTP expired.',
            ];
        }

        $otp->increment('attempts');

        if (!$this->otpService->verify($otp->otp, $code)) {
            return [
                'success' => false,
                'message' => 'OTP is invalid.',
            ];
        }

        $otp->verified = true;
        $otp->verified_at = now();
        $otp->save();

        $user = User::firstOrCreate(
            [
                'mobile' => $mobile,
            ],
            [
                'name' => null,
                'email' => null,
                'password' => bcrypt(Str::random(32)),
                'mobile_verified' => true,
                'is_active' => true,
            ]
        );

        $user->mobile_verified = true;
        $user->last_login_at = now();
        $user->save();

        // حذف توکن‌های قبلی (اختیاری)
        $user->tokens()->delete();

        $token = $user->createToken('goldplatform')->plainTextToken;

        return [
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'mobile' => $user->mobile,
                'name' => $user->name,
            ],
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}