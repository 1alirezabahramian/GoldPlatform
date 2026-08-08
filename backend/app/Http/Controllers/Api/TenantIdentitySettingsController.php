<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use App\Support\IdentityOnboardingPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TenantIdentitySettingsController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        return ApiResponse::success([
            'customer_auth_mode' => $tenant->customer_auth_mode,
            'staff_auth_mode' => $tenant->staff_auth_mode,
            'customer_registration_mode' => $tenant->customer_registration_mode,
            'available_registration_modes' => IdentityOnboardingPolicy::registrationModes(),
            'readiness' => [
                'jibit' => false,
                'kimia_customer_create' => false,
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_registration_mode' => [
                'required',
                Rule::in(IdentityOnboardingPolicy::registrationModes()),
            ],
        ]);

        $tenant = $request->attributes->get('tenant');
        $mode = $validated['customer_registration_mode'];

        // Fail closed until both external dependencies have repository/runtime Ground Truth.
        $jibitReady = false;
        $kimiaCustomerCreateReady = false;

        if (! IdentityOnboardingPolicy::canActivateRegistrationMode($mode, $jibitReady, $kimiaCustomerCreateReady)) {
            return ApiResponse::error(
                message: 'This registration mode cannot be activated until its external dependencies are verified.',
                errors: [
                    'code' => 'REGISTRATION_MODE_DEPENDENCY_NOT_READY',
                    'readiness' => [
                        'jibit' => $jibitReady,
                        'kimia_customer_create' => $kimiaCustomerCreateReady,
                    ],
                ],
                status: 409
            );
        }

        $tenant->forceFill([
            'customer_registration_mode' => $mode,
        ])->save();

        return ApiResponse::success([
            'customer_auth_mode' => $tenant->customer_auth_mode,
            'staff_auth_mode' => $tenant->staff_auth_mode,
            'customer_registration_mode' => $tenant->customer_registration_mode,
        ], 'Identity and onboarding settings updated.');
    }
}
