<?php

namespace App\Services\Sms\Contracts;

use App\Services\Sms\DTO\SmsResult;

interface SmsProvider
{
    public function sendVerify(
        string $mobile,
        string $template,
        array $parameters
    ): SmsResult;
}
