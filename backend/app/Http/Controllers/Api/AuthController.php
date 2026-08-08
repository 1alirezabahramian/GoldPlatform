<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\Otp;
use App\Models\User;
use App\Services\Sms\OtpService;
use App\Services\Sms\SmsService;
use App\Support\ApiResponse;
use App\Tenancy\TenantContext;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected OtpService $otpService,
        protected SmsService $smsService,
        protected TenantContext $tenantContext
    ) {
    }

    public function sendOtp(SendOtpRequest $request)
    {
        $tenant = $this->tenantContext->tenant();
        $user = User::query()
            ->where('tenant_id', $tenant->id)
            ->where('mobile', $request->mobile)
            ->first();

        if (! $user) {
            return ApiResponse::error(
                message: 'شما هنوز عضو GoldPlatform نیستید.',
                errors: ['code' => 'CUSTOMER_NOT_REGISTERED'],
                status: 404,
            );
        }

        $otp = $this->otpService->create($request->mobile);

        $result = $this->smsService->sendOtp(
            $request->mobile,
            $otp->otp
        );

        if (! $result->success) {
            return ApiResponse::error($result->message);
        }

        return ApiResponse::success(
            message: 'OTP Sent Successfully'
        );
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $tenant = $this->tenantContext->tenant();
        $user = User::query()
            ->where('tenant_id', $tenant->id)
            ->where('mobile', $request->mobile)
            ->first();

        if (! $user) {
            return ApiResponse::error(
                message: 'شما هنوز عضو GoldPlatform نیستید.',
                errors: ['code' => 'CUSTOMER_NOT_REGISTERED'],
                status: 404,
            );
        }

        $otp = Otp::query()
            ->where('mobile', $request->mobile)
            ->where('purpose', 'login')
            ->where('verified', false)
            ->latest('id')
            ->first();

        if (! $otp || ! $this->otpService->verify($otp, $request->otp)) {
            return ApiResponse::error(
                message: 'کد تایید معتبر نیست یا منقضی شده است.',
                errors: ['code' => 'OTP_INVALID_OR_EXPIRED'],
                status: 422,
            );
        }

        $user->forceFill([
            'mobile_verified' => true,
            'last_login_at' => now(),
        ])->save();

        $token = $user->createToken('customer-mobile')->plainTextToken;

        return ApiResponse::success([
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'ورود با موفقیت انجام شد.');
    }

    public function logout(Request $request)
    {
        $token = $request->user()?->currentAccessToken();

        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        return ApiResponse::success(
            message: 'خروج با موفقیت انجام شد.'
        );
    }
}
