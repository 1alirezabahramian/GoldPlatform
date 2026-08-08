<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Identity\TenantStaffProvisioningService;
use App\Support\ApiResponse;
use App\Support\TenantOwnerAuthority;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class TenantStaffController extends Controller
{
    public function store(Request $request, TenantStaffProvisioningService $provisioning): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');
        $actor = $request->user();

        if (! $tenant || ! $actor || ! TenantOwnerAuthority::allows($actor, $tenant)) {
            return ApiResponse::error('Tenant owner authority is required.', status: 403);
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'size:11', Rule::unique('users', 'mobile')],
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->where(fn ($query) => $query->where('tenant_id', $tenant->id)),
            ],
            'role' => ['required', Rule::in(['admin', 'operator'])],
        ]);

        $result = $provisioning->provision(
            tenant: $tenant,
            actor: $actor,
            attributes: $validated,
            requestId: $request->header('X-Request-ID'),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        $user = $result['user'];

        return ApiResponse::success([
            'staff' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'username' => $user->username,
                'role' => $validated['role'],
                'must_change_password' => true,
                'tenant_id' => $tenant->id,
            ],
            'temporary_password' => $result['temporary_password'],
        ], 'Staff account created successfully.', status: 201)
            ->header('Cache-Control', 'no-store, private');
    }
}
