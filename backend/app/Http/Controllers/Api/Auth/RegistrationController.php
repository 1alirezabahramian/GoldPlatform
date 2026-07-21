<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\Auth\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    public function __construct(
        protected RegistrationService $registrationService
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        Log::info('========== REGISTER REQUEST ==========');

        Log::info('Request Data', $request->all());

        Log::info('Validation Passed');

        $validated = $request->validated();

        Log::info('Validated Data', $validated);

        $user = $this->registrationService->register($validated);

        Log::info('User Created', [
            'id' => $user->id,
            'mobile' => $user->mobile,
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        Log::info('Token Created');

        return response()->json([
            'success' => true,
            'message' => 'Registration completed successfully.',
            'token' => $token,
            'user' => $user->load([
                'wallet',
                'group',
                'account',
            ]),
        ]);
    }
}