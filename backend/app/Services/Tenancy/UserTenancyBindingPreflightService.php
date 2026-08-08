<?php

namespace App\Services\Tenancy;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserTenancyBindingPreflightService
{
    /**
     * Inspect the current users table without mutating data.
     *
     * This deliberately does not infer a Tenant from mobile, account, domain,
     * national code, account code, or any other identity field.
     *
     * @return array<string, bool|int>
     */
    public function inspect(): array
    {
        if (! Schema::hasTable('users')) {
            return [
                'users_table_exists' => false,
                'tenant_id_column_exists' => false,
                'total_users' => 0,
                'linked_users' => 0,
                'unlinked_users' => 0,
                'duplicate_account_bindings' => 0,
                'users_missing_tenant_assignment' => 0,
                'unique_account_binding_preflight_passes' => false,
                'authenticated_tenancy_activation_ready' => false,
            ];
        }

        $tenantColumnExists = Schema::hasColumn('users', 'tenant_id');
        $totalUsers = DB::table('users')->count();
        $linkedUsers = DB::table('users')->whereNotNull('account_id')->count();
        $duplicateAccountBindings = DB::table('users')
            ->select('account_id')
            ->whereNotNull('account_id')
            ->groupBy('account_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $usersMissingTenantAssignment = $tenantColumnExists
            ? DB::table('users')->whereNull('tenant_id')->count()
            : $totalUsers;

        return [
            'users_table_exists' => true,
            'tenant_id_column_exists' => $tenantColumnExists,
            'total_users' => $totalUsers,
            'linked_users' => $linkedUsers,
            'unlinked_users' => $totalUsers - $linkedUsers,
            'duplicate_account_bindings' => $duplicateAccountBindings,
            'users_missing_tenant_assignment' => $usersMissingTenantAssignment,
            'unique_account_binding_preflight_passes' => $duplicateAccountBindings === 0,
            'authenticated_tenancy_activation_ready' => $tenantColumnExists
                && $usersMissingTenantAssignment === 0,
        ];
    }
}
