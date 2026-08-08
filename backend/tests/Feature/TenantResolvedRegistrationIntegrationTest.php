<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\Auth\RegistrationController;
use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TenantResolvedRegistrationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/_v2/test-register', [RegistrationController::class, 'registerForResolvedTenant'])
            ->middleware('tenant.resolve');
    }

    public function test_verified_temporary_host_registers_user_for_explicit_pilot_tenant(): void
    {
        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();

        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'host' => 'v2-pilot.test',
            'is_primary' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        $response = $this->postJson('http://v2-pilot.test/_v2/test-register', [
            'mobile' => '09120000001',
            'first_name' => 'V2',
            'last_name' => 'Pilot',
            'password' => 'secret123',
        ]);

        $response->assertOk();

        $user = User::query()->where('mobile', '09120000001')->firstOrFail();

        $this->assertSame((int) $tenant->id, (int) $user->tenant_id);
        $this->assertNotNull($user->wallet);
    }

    public function test_unknown_temporary_host_fails_closed_without_creating_user(): void
    {
        $response = $this->postJson('http://unknown-v2.test/_v2/test-register', [
            'mobile' => '09120000002',
            'first_name' => 'V2',
            'last_name' => 'Unknown',
            'password' => 'secret123',
        ]);

        $response->assertNotFound();
        $this->assertFalse(User::query()->where('mobile', '09120000002')->exists());
    }
}
