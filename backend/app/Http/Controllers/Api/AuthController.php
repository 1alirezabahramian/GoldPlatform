<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendOtpRequest;
use App\Models\Otp;
use App\Services\Sms\OtpService;
use App\Services\Sms\SmsService;
use App\Support\ApiResponse;

class AuthController extends Controller
{
    public function __construct(

        protected OtpService $otpService,

        protected SmsService $smsService

    ) {
    }

    public function sendOtp(SendOtpRequest $request)
    {
        $otp = $this->otpService->create(

            $request->mobile

        );

        $result = $this->smsService->sendOtp(

            $request->mobile,

            $otp->otp

        );

        if (! $result->success) {

            return ApiResponse::error(

                $result->message

            );
        }

        return ApiResponse::success(

            message: 'OTP Sent Successfully'

        );
    }

    public function verifyOtp()
    {
        //
    }

    public function logout()
    {
        //
    }
}