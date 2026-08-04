<?php

namespace App\ReadModels;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class AdminAccessControlReadModel
{
    /** @return array<string, mixed> */
    public function roles(): array
    {
        $roles = Role::query()
            ->withCount('users')
            ->with(['permissions:id,name,guard_name'])
            ->orderBy('name')
            ->get();

        return [
            'items' => $roles->map(fn (Role $role): array => [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'users_count' => (int) $role->users_count,
                'permissions' => $role->permissions
                    ->pluck('name')
                    ->sort()
                    ->values()
                    ->all(),
            ])->all(),
        ];
    }

    /** @return array<string, mixed> */
    public function permissions(): array
    {
        $permissions = Permission::query()
            ->withCount('roles')
            ->orderBy('name')
            ->get(['id', 'name', 'guard_name']);

        return [
            'items' => $permissions->map(fn (Permission $permission): array => [
                'id' => $permission->id,
                'name' => $permission->name,
                'guard_name' => $permission->guard_name,
                'roles_count' => (int) $permission->roles_count,
                'classification' => $this->classify($permission->name),
            ])->all(),
        ];
    }

    /** @return array<string, mixed> */
    public function matrix(): array
    {
        $roles = Role::query()
            ->with(['permissions:id,name'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $permissions = Permission::query()
            ->orderBy('name')
            ->pluck('name')
            ->values();

        return [
            'roles' => $roles->pluck('name')->values()->all(),
            'permissions' => $permissions->all(),
            'matrix' => $roles->mapWithKeys(function (Role $role) use ($permissions): array {
                $assigned = $role->permissions->pluck('name')->flip();

                return [
                    $role->name => $permissions->mapWithKeys(
                        fn (string $permission): array => [$permission => $assigned->has($permission)]
                    )->all(),
                ];
            })->all(),
        ];
    }

    private function classify(string $permission): string
    {
        if (str_ends_with($permission, '.access')) {
            return 'access';
        }

        if (str_contains($permission, '.view')) {
            return 'read';
        }

        if (str_contains($permission, '.approve') || str_contains($permission, '.reject')) {
            return 'approval';
        }

        if (
            str_contains($permission, '.create')
            || str_contains($permission, '.update')
            || str_contains($permission, '.submit')
            || str_contains($permission, '.complete')
            || str_contains($permission, '.ready')
        ) {
            return 'write';
        }

        return 'other';
    }
}
