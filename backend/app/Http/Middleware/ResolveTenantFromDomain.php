<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use App\Tenancy\TenantContext;
use App\Tenancy\TenantResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantFromDomain
{
    public function __construct(
        private TenantResolver $resolver,
        private TenantContext $context
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolver->resolveHost($request->getHost());

        if ($tenant === null) {
            return ApiResponse::error(
                message: 'Service unavailable for this domain.',
                status: Response::HTTP_NOT_FOUND
            );
        }

        $this->context->activate($tenant);
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}
