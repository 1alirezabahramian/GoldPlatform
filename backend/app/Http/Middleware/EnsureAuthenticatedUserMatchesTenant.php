<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use App\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticatedUserMatchesTenant
{
    public function __construct(private TenantContext $context)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->context->tenantOrNull();
        $user = $request->user();

        if ($tenant === null || $user === null) {
            return ApiResponse::error(
                message: 'Tenant context is not available for this authenticated request.',
                status: Response::HTTP_FORBIDDEN
            );
        }

        if ($user->tenant_id === null || (int) $user->tenant_id !== (int) $tenant->id) {
            return ApiResponse::error(
                message: 'Authenticated user does not belong to the resolved tenant.',
                status: Response::HTTP_FORBIDDEN
            );
        }

        return $next($request);
    }
}
