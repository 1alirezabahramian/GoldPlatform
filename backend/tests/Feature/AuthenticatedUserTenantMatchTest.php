<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureAuthenticatedUserMatchesTenant;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthenticatedUserTenantMatchTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function matching_authenticated_user_is_allowed(): void
    {
        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();
        $user = User::factory()->create([
            'mobile' => '09120000101',
            'tenant_id' => $tenant->id,
        ]);

        $context = app(TenantContext::class);
        $context->activate($tenant);

        $request = Request::create('/api/v1/customer/dashboard', 'GET');
        $request->setUserResolver(fn () => $user);

        $response = app(EnsureAuthenticatedUserMatchesTenant::class)->handle(
            $request,
            fn () => response()->noContent()
        );

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    #[Test]
    public function cross_tenant_authenticated_user_is_rejected(): void
    {
        $resolvedTenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();
        $otherTenant = Tenant::create([
            'name' => 'Other Tenant',
            'slug' => 'other-tenant',
            'is_active' => true,
        ]);
        $user = User::factory()->create([
            'mobile' => '09120000102',
            'tenant_id' => $otherTenant->id,
        ]);

        $context = app(TenantContext::class);
        $context->activate($resolvedTenant);

        $request = Request::create('/api/v1/customer/dashboard', 'GET');
        $request->setUserResolver(fn () => $user);

        $response = app(EnsureAuthenticatedUserMatchesTenant::class)->handle(
            $request,
            fn () => response()->noContent()
        );

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    #[Test]
    public function missing_tenant_context_or_assignment_is_rejected(): void
    {
        $user = User::factory()->create([
            'mobile' => '09120000103',
            'tenant_id' => null,
        ]);

        $request = Request::create('/api/v1/customer/dashboard', 'GET');
        $request->setUserResolver(fn () => $user);

        $response = app(EnsureAuthenticatedUserMatchesTenant::class)->handle(
            $request,
            fn () => response()->noContent()
        );

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
