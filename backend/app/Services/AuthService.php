<?php

namespace App\Services;

class AuthService
{
    public function sendOtp(string $mobile): array
    {
        return [
            'success' => true,
            'message' => 'OTP sent successfully.',
        ];
    }

    public function verifyOtp(string $mobile, string $otp): array
    {
        return [
            'success' => true,
            'token' => 'temporary-token',
        ];
    }
}