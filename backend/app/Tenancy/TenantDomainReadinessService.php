<?php

namespace App\Tenancy;

use App\Models\Tenant;
use App\Models\TenantDomain;

class TenantDomainReadinessService
{
    public function inspect(string $tenantSlug, string $host): array
    {
        $normalizedHost = TenantHost::normalize($host);
        $tenant = Tenant::query()->where('slug', $tenantSlug)->first();
        $domain = $normalizedHost === ''
            ? null
            : TenantDomain::query()->where('host', $normalizedHost)->first();

        $tenantFound = $tenant !== null;
        $domainFound = $domain !== null;
        $domainBelongsToTenant = $tenantFound
            && $domainFound
            && (int) $domain->tenant_id === (int) $tenant->id;
        $tenantActive = $tenantFound && (bool) $tenant->is_active;
        $domainActive = $domainFound && (bool) $domain->is_active;
        $domainVerified = $domainFound && $domain->verified_at !== null;

        return [
            'tenant_slug' => $tenantSlug,
            'host' => $normalizedHost,
            'tenant_found' => $tenantFound,
            'tenant_active' => $tenantActive,
            'domain_found' => $domainFound,
            'domain_belongs_to_tenant' => $domainBelongsToTenant,
            'domain_active' => $domainActive,
            'domain_verified' => $domainVerified,
            'runtime_activation_ready' => $tenantFound
                && $tenantActive
                && $domainFound
                && $domainBelongsToTenant
                && $domainActive
                && $domainVerified,
        ];
    }
}
