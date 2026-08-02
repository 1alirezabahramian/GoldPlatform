<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Tenancy\TenantContext;
use App\Tenancy\TenantResolver;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use LogicException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantDomainResolutionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resolves_only_a_verified_active_domain_and_tenant(): void
    {
        $tenant = $this->tenant('shop-one');

        TenantDomain::create([
            'tenant_id' => $tenant->id,
            'host' => 'SHOP-ONE.EXAMPLE.TEST.',
            'is_primary' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        $resolved = app(TenantResolver::class)
            ->resolveHost('shop-one.example.test:443');

        $this->assertTrue($tenant->is($resolved));
    }

    #[Test]
    public function it_never_falls_back_for_unknown_unverified_or_inactive_domains(): void
    {
        $activeTenant = $this->tenant('active-shop');
        $inactiveTenant = $this->tenant('inactive-shop', false);

        TenantDomain::create([
            'tenant_id' => $activeTenant->id,
            'host' => 'unverified.example.test',
            'verified_at' => null,
        ]);

        TenantDomain::create([
            'tenant_id' => $activeTenant->id,
            'host' => 'disabled-domain.example.test',
            'is_active' => false,
            'verified_at' => now(),
        ]);

        TenantDomain::create([
            'tenant_id' => $inactiveTenant->id,
            'host' => 'inactive-shop.example.test',
            'verified_at' => now(),
        ]);

        $resolver = app(TenantResolver::class);

        foreach ([
            'unknown.example.test',
            'unverified.example.test',
            'disabled-domain.example.test',
            'inactive-shop.example.test',
        ] as $host) {
            $this->assertNull($resolver->resolveHost($host));
        }
    }

    #[Test]
    public function tenant_domain_hosts_are_globally_unique(): void
    {
        $first = $this->tenant('first-shop');
        $second = $this->tenant('second-shop');

        TenantDomain::create([
            'tenant_id' => $first->id,
            'host' => 'shared.example.test',
            'verified_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        TenantDomain::create([
            'tenant_id' => $second->id,
            'host' => 'SHARED.EXAMPLE.TEST.',
            'verified_at' => now(),
        ]);
    }

    #[Test]
    public function middleware_sets_a_trusted_context_and_rejects_an_unknown_host(): void
    {
        $tenant = $this->tenant('resolved-shop');

        TenantDomain::create([
            'tenant_id' => $tenant->id,
            'host' => 'resolved.example.test',
            'verified_at' => now(),
        ]);

        Route::middleware('tenant.resolve')->get(
            '/_tests/tenant-context',
            function (Request $request, TenantContext $context) {
                return response()->json([
                    'context' => $context->tenant()->slug,
                    'request' => $request->attributes->get('tenant')->slug,
                ]);
            }
        );

        $this->withHeader('Host', 'resolved.example.test')
            ->getJson('/_tests/tenant-context')
            ->assertOk()
            ->assertExactJson([
                'context' => 'resolved-shop',
                'request' => 'resolved-shop',
            ]);

        $this->withHeader('Host', 'unknown.example.test')
            ->getJson('/_tests/tenant-context')
            ->assertNotFound()
            ->assertJsonPath('success', false)
            ->assertJsonMissing(['tenant' => 'resolved-shop']);
    }

    #[Test]
    public function tenant_context_cannot_switch_inside_one_execution_scope(): void
    {
        $context = new TenantContext();
        $context->activate($this->tenant('first-context'));

        $this->expectException(LogicException::class);

        $context->activate($this->tenant('second-context'));
    }

    private function tenant(string $slug, bool $active = true): Tenant
    {
        return Tenant::create([
            'name' => str_replace('-', ' ', $slug),
            'slug' => $slug,
            'is_active' => $active,
        ]);
    }
}
