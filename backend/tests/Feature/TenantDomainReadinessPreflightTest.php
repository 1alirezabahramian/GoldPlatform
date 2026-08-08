<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Tenancy\TenantDomainReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantDomainReadinessPreflightTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fails_closed_when_domain_is_missing(): void
    {
        $result = app(TenantDomainReadinessService::class)
            ->inspect('khalifeh-coin', 'khalifehcoin.com');

        $this->assertTrue($result['tenant_found']);
        $this->assertFalse($result['domain_found']);
        $this->assertFalse($result['runtime_activation_ready']);
    }

    public function test_it_fails_closed_for_unverified_domain(): void
    {
        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();

        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'host' => 'khalifehcoin.com',
            'is_primary' => true,
            'is_active' => true,
            'verified_at' => null,
        ]);

        $result = app(TenantDomainReadinessService::class)
            ->inspect('khalifeh-coin', 'WWW.KhalifehCoin.com');

        $this->assertTrue($result['domain_found']);
        $this->assertTrue($result['domain_belongs_to_tenant']);
        $this->assertFalse($result['domain_verified']);
        $this->assertFalse($result['runtime_activation_ready']);
    }

    public function test_it_is_ready_only_for_verified_active_domain_of_target_tenant(): void
    {
        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();

        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'host' => 'khalifehcoin.com',
            'is_primary' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        $result = app(TenantDomainReadinessService::class)
            ->inspect('khalifeh-coin', 'khalifehcoin.com');

        $this->assertTrue($result['tenant_active']);
        $this->assertTrue($result['domain_active']);
        $this->assertTrue($result['domain_verified']);
        $this->assertTrue($result['runtime_activation_ready']);
    }
}
