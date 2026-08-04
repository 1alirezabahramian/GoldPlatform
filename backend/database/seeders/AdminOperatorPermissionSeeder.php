<?php

namespace Database\Seeders;

use App\Support\AdminOperatorPermissionCatalog;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminOperatorPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (AdminOperatorPermissionCatalog::all() as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        Role::findOrCreate('admin', 'web')
            ->syncPermissions(AdminOperatorPermissionCatalog::adminDefaults());

        Role::findOrCreate('operator', 'web')
            ->syncPermissions(AdminOperatorPermissionCatalog::operatorDefaults());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
