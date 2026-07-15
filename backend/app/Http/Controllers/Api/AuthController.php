<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\VerifyOtpRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'OTP generated successfully.',
            'mobile' => $request->mobile,
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => 'temporary-token',
        ]);
    }

    public function logout(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
        ]);
    }
}