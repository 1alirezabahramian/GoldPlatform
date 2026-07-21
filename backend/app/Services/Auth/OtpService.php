<?php

namespace App\Services\Auth;

use App\Models\OtpCode;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    /**
     * تولید کد OTP
     */
    public function generate(): string
    {
        return (string) random_int(100000, 999999);
    }

    /**
     * ایجاد OTP
     */
    public function create(string $mobile, string $purpose): OtpCode
    {
        $code = $this->generate();

        return OtpCode::create([

            'mobile' => $mobile,

            'code' => Hash::make($code),

            'purpose' => $purpose,

            'expires_at' => now()->addMinutes(2),

            'attempts' => 0,

            'resend_count' => 0,

            'ip_address' => request()->ip(),

            'user_agent' => request()->userAgent(),

        ])->setAttribute('plain_code', $code);
    }

    /**
     * بررسی کد
     */
    public function verify(OtpCode $otp, string $code): bool
    {
        if ($otp->isExpired()) {
            return false;
        }

        if ($otp->isVerified()) {
            return false;
        }

        $otp->increment('attempts');

        if (! Hash::check($code, $otp->code)) {
            return false;
        }

        $otp->update([
            'verified_at' => now(),
        ]);

        return true;
    }
}