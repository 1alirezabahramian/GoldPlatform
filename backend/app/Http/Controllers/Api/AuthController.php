<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->sendOtp($request->mobile);

        return response()->json($result);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtp(
            $request->mobile,
            $request->otp
        );

        return response()->json($result);
    }

    public function logout(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
        ]);
    }
}