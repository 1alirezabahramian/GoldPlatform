<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Tenant;
use App\Services\Auth\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class RegistrationController extends Controller
{
    public function __construct(
        protected RegistrationService $registrationService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        Log::info('========== REGISTER REQUEST ==========');

        $validated = $request->validated();

        $user = $this->registrationService->register($validated);

        return $this->registrationResponse($user);
    }

    public function registerForResolvedTenant(RegisterRequest $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        if (! $tenant instanceof Tenant) {
            throw new RuntimeException('Resolved tenant context is required for tenant-aware registration.');
        }

        $user = $this->registrationService->registerForTenant(
            $request->validated(),
            $tenant
        );

        return $this->registrationResponse($user);
    }

    private function registrationResponse($user): JsonResponse
    {
        Log::info('User Created', [
            'id' => $user->id,
            'mobile' => $user->mobile,
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration completed successfully.',
            'token'   => $token,
            'user'    => $user,
        ]);
    }
}
