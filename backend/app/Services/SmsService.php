<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * ارسال کد OTP
     */
    public function sendOtp(string $mobile, string $otp): bool
    {
        // فعلاً فقط لاگ می‌گیریم
        // در Sprint بعدی این قسمت به SMS.ir متصل می‌شود.

        Log::info('Mock SMS Sent', [
            'mobile' => $mobile,
            'otp' => $otp,
        ]);

        return true;
    }

    /**
     * ارسال پیام متنی عمومی
     */
    public function send(string $mobile, string $message): bool
    {
        Log::info('Mock SMS Message', [
            'mobile' => $mobile,
            'message' => $message,
        ]);

        return true;
    }
}