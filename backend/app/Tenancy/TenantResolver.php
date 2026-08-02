<?php

namespace App\Tenancy;

use App\Models\Tenant;
use App\Models\TenantDomain;

class TenantResolver
{
    public function resolveHost(string $host): ?Tenant
    {
        $normalizedHost = TenantHost::normalize($host);

        if ($normalizedHost === '') {
            return null;
        }

        $domain = TenantDomain::query()
            ->with('tenant')
            ->where('host', $normalizedHost)
            ->where('is_active', true)
            ->whereNotNull('verified_at')
            ->whereHas(
                'tenant',
                fn ($query) => $query->where('is_active', true)
            )
            ->first();

        return $domain?->tenant;
    }
}
