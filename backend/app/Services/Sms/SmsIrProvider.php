<?php

namespace App\Services\Sms\Providers;

use App\Services\Sms\Contracts\SmsProvider;
use App\Services\Sms\DTO\SmsResult;

class SmsIrProvider implements SmsProvider
{
    public function sendVerify(
        string $mobile,
        string $template,
        array $parameters
    ): SmsResult {

        // TODO:
        // اتصال واقعی به SMS.ir

        return new SmsResult(
            success: false,
            message: 'SMS.ir Provider is not implemented yet.'
        );
    }
}