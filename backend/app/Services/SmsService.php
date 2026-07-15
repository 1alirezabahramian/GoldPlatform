<?php

namespace App\Services;

class SmsService
{
    public function send(string $mobile, string $message): bool
    {
        // اتصال به SMS.ir در Sprint بعد
        return true;
    }
}