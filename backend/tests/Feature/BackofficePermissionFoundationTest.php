<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\BackofficePermissionCatalog;
use Database\Seeders\BackofficePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class BackofficePermissionFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_seeder_is_idempotent_and_preserves_existing_role_permissions(): void
    {
        $legacyPermission = Permission::findOrCreate('legacy.reports.view', 'web');
        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->givePermissionTo($legacyPermission);

        $this->seed(BackofficePermissionSeeder::class);
        $this->seed(BackofficePermissionSeeder::class);

        $adminRole->refresh();
        $operatorRole = Role::findByName('operator', 'web');

        $this->assertTrue($adminRole->hasPermissionTo('legacy.reports.view'));
        $this->assertTrue($adminRole->hasPermissionTo(BackofficePermissionCatalog::ADMIN_ACCESS));
        $this->assertTrue($operatorRole->hasPermissionTo(BackofficePermissionCatalog::OPERATOR_ACCESS));
        $this->assertSame(1, Permission::query()->where('name', BackofficePermissionCatalog::ADMIN_ACCESS)->count());
        $this->assertSame(1, Permission::query()->where('name', BackofficePermissionCatalog::OPERATOR_ACCESS)->count());
    }

    public function test_seeder_does_not_remove_direct_user_permissions(): void
    {
        $user = User::factory()->create();
        $directPermission = Permission::findOrCreate('legacy.direct.permission', 'web');
        $user->givePermissionTo($directPermission);

        $this->seed(BackofficePermissionSeeder::class);

        $user->refresh();

        $this->assertTrue($user->hasDirectPermission('legacy.direct.permission'));
    }

    public function test_catalog_contains_only_verified_foundation_permissions(): void
    {
        $this->assertSame([
            'admin.access',
            'operator.access',
        ], BackofficePermissionCatalog::all());
    }
}
