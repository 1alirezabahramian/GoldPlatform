<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\CustomerApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerProfileController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        return CustomerApiResponse::success($request, [
            'profile' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'display_name' => $user->name,
                'mobile' => $user->mobile,
                'mobile_verified' => (bool) $user->mobile_verified,
                'is_active' => (bool) $user->is_active,
                'roles' => $user->getRoleNames()->values()->all(),
                'last_login_at' => $user->last_login_at?->toIso8601String(),
            ],
        ]);
    }
}
