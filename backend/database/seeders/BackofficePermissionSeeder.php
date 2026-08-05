<?php

namespace Database\Seeders;

use App\Support\BackofficePermissionCatalog;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class BackofficePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (BackofficePermissionCatalog::all() as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        $adminRole = Role::findOrCreate('admin', 'web');
        $operatorRole = Role::findOrCreate('operator', 'web');

        $adminRole->givePermissionTo(BackofficePermissionCatalog::adminDefaults());
        $operatorRole->givePermissionTo(BackofficePermissionCatalog::operatorDefaults());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
