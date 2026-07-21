<?php

namespace App\Services\Sms;

use App\Services\Sms\Contracts\SmsProvider;
use App\Services\Sms\DTO\SmsResult;

class SmsService
{
    public function __construct(
        protected SmsProvider $provider
    ) {
    }

    public function sendOtp(
        string $mobile,
        string $code
    ): SmsResult {

        return $this->provider->sendVerify(

            $mobile,

            SmsTemplate::LoginOTP->value,

            [
                'Code' => $code,
            ]

        );
    }
}