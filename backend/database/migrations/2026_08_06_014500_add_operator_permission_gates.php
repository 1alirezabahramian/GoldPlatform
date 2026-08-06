<?php

use App\Support\OperatorPermissionCatalog;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (OperatorPermissionCatalog::all() as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        foreach (['operator', 'admin'] as $roleName) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->givePermissionTo(OperatorPermissionCatalog::all());
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['operator', 'admin'] as $roleName) {
            $role = Role::query()
                ->where('name', $roleName)
                ->where('guard_name', 'web')
                ->first();

            if ($role !== null) {
                $role->revokePermissionTo(OperatorPermissionCatalog::all());
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
