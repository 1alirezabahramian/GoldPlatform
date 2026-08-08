<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use App\Support\OperatorPermissionCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OperatorPermissionGateTest extends TestCase
{
    use RefreshDatabase;

    private const TENANT_HOST = 'operator-permission-pilot.test';

    protected function setUp(): void
    {
        parent::setUp();

        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();

        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'host' => self::TENANT_HOST,
            'is_primary' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);
    }

    public function test_default_operator_and_admin_roles_keep_existing_access(): void
    {
        foreach (['operator', 'admin'] as $roleName) {
            $role = Role::findByName($roleName, 'web');

            foreach (OperatorPermissionCatalog::all() as $permissionName) {
                $this->assertTrue(
                    $role->hasPermissionTo($permissionName),
                    "{$roleName} must keep {$permissionName} after permission bootstrap.",
                );
            }
        }
    }

    public function test_each_operator_route_has_an_explicit_permission_gate(): void
    {
        $expected = [
            'api/operator/orders/queue' => OperatorPermissionCatalog::ORDERS_QUEUE_VIEW,
            'api/operator/deliveries/queue' => OperatorPermissionCatalog::DELIVERIES_QUEUE_VIEW,
            'api/operator/deliveries/{deliveryRequest}/approve' => OperatorPermissionCatalog::DELIVERIES_APPROVE,
            'api/operator/deliveries/{deliveryRequest}/ready' => OperatorPermissionCatalog::DELIVERIES_READY,
            'api/operator/deliveries/{deliveryRequest}/deliver' => OperatorPermissionCatalog::DELIVERIES_COMPLETE,
        ];

        foreach ($expected as $uri => $permissionName) {
            $route = collect(Route::getRoutes()->getRoutes())
                ->first(fn ($candidate) => $candidate->uri() === $uri);

            $this->assertNotNull($route, "Missing route {$uri}.");
            $this->assertContains(
                'permission:'.$permissionName,
                $route->gatherMiddleware(),
                "Route {$uri} must require {$permissionName}.",
            );
        }
    }

    public function test_operator_can_be_restricted_without_removing_operator_role(): void
    {
        $role = Role::findByName('operator', 'web');
        $role->revokePermissionTo(OperatorPermissionCatalog::ORDERS_QUEUE_VIEW);

        $tenant = Tenant::query()->where('slug', 'khalifeh-coin')->firstOrFail();
        $operator = User::factory()->create(['tenant_id' => $tenant->id]);
        $operator->assignRole($role);
        Sanctum::actingAs($operator);

        $this->getJson($this->tenantUrl('/api/operator/orders/queue'))->assertForbidden();
        $this->getJson($this->tenantUrl('/api/operator/deliveries/queue'))->assertOk();
    }

    private function tenantUrl(string $path): string
    {
        return 'http://'.self::TENANT_HOST.$path;
    }
}
