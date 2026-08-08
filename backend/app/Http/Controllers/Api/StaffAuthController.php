<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ApiResponse;
use App\Support\IdentityOnboardingPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StaffAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string'],
        ]);

        $tenant = $request->attributes->get('tenant');

        if (! $tenant || ! $tenant->is_active || $tenant->staff_auth_mode !== IdentityOnboardingPolicy::STAFF_AUTH_PASSWORD) {
            return ApiResponse::error('Staff sign-in is unavailable.', status: 403);
        }

        $user = User::query()
            ->where('tenant_id', $tenant->id)
            ->where('username', $validated['username'])
            ->where('is_active', true)
            ->first();

        if (! $user || ! $user->password || ! Hash::check($validated['password'], $user->password) || ! $user->hasAnyRole(['admin', 'operator'])) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are invalid.'],
            ]);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        return ApiResponse::success([
            'token' => $user->createToken('staff')->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'roles' => $user->getRoleNames()->values(),
                'must_change_password' => (bool) $user->must_change_password,
            ],
        ], 'Signed in successfully.');
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:12', 'confirmed', 'different:current_password'],
        ]);

        /** @var User $user */
        $user = $request->user();

        if (! $user->hasAnyRole(['admin', 'operator']) || ! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is invalid.'],
            ]);
        }

        $user->forceFill([
            'password' => $validated['password'],
            'must_change_password' => false,
            'password_changed_at' => now(),
        ])->save();

        $currentTokenId = $user->currentAccessToken()?->id;
        $user->tokens()->when($currentTokenId, fn ($query) => $query->where('id', '!=', $currentTokenId))->delete();

        return ApiResponse::success([
            'must_change_password' => false,
            'password_changed_at' => $user->password_changed_at?->toISOString(),
        ], 'Password changed successfully.');
    }
}
