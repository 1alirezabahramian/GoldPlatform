<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Services\Auth\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantAwareRegistrationServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tenant_aware_registration_assigns_the_explicit_tenant(): void
    {
        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();

        $user = app(RegistrationService::class)->registerForTenant([
            'mobile' => '09120000991',
            'password' => 'secret-pass',
            'first_name' => 'Tenant',
            'last_name' => 'Bound',
        ], $tenant);

        $this->assertSame($tenant->id, $user->tenant_id);
    }

    #[Test]
    public function legacy_registration_path_does_not_infer_a_tenant(): void
    {
        $user = app(RegistrationService::class)->register([
            'mobile' => '09120000992',
            'password' => 'secret-pass',
            'first_name' => 'Legacy',
            'last_name' => 'Unbound',
        ]);

        $this->assertNull($user->tenant_id);
    }
}
