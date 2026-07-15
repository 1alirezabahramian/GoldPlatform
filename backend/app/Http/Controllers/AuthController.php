<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\VerifyOtpRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * ارسال کد تایید
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'کد تایید با موفقیت ارسال شد.',
            'data' => [
                'mobile' => $request->mobile,
            ],
        ]);
    }

    /**
     * تایید کد پیامک
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'ورود با موفقیت انجام شد.',
            'token' => 'temporary-token',
            'user' => [
                'mobile' => $request->mobile,
            ],
        ]);
    }

    /**
     * خروج از حساب
     */
    public function logout(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'با موفقیت خارج شدید.',
        ]);
    }
}