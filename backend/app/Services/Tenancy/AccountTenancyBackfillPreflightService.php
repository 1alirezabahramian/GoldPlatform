<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class AccountTenancyBackfillPreflightService
{
    /**
     * Inspect whether existing accounts can be safely backfilled to one explicit Tenant.
     * This service is read-only and never mutates account ownership.
     *
     * @return array<string, int|bool|string>
     */
    public function inspect(string $tenantSlug): array
    {
        $tenant = Tenant::query()->where('slug', $tenantSlug)->first();

        if ($tenant === null) {
            return [
                'tenant_found' => false,
                'tenant_slug' => $tenantSlug,
                'account_tenant_column_exists' => Schema::hasColumn('accounts', 'tenant_id'),
                'total_accounts' => 0,
                'accounts_missing_tenant_assignment' => 0,
                'accounts_linked_to_target_tenant_users' => 0,
                'accounts_linked_to_other_tenant_users' => 0,
                'accounts_linked_to_multiple_tenants' => 0,
                'unlinked_accounts' => 0,
                'backfill_safe' => false,
            ];
        }

        $columnExists = Schema::hasColumn('accounts', 'tenant_id');
        $totalAccounts = (int) DB::table('accounts')->count();
        $missing = $columnExists
            ? (int) DB::table('accounts')->whereNull('tenant_id')->count()
            : $totalAccounts;

        $linkedTarget = (int) DB::table('accounts')
            ->join('users', 'users.account_id', '=', 'accounts.id')
            ->where('users.tenant_id', $tenant->id)
            ->distinct('accounts.id')
            ->count('accounts.id');

        $linkedOther = (int) DB::table('accounts')
            ->join('users', 'users.account_id', '=', 'accounts.id')
            ->whereNotNull('users.tenant_id')
            ->where('users.tenant_id', '!=', $tenant->id)
            ->distinct('accounts.id')
            ->count('accounts.id');

        $multipleTenants = (int) DB::query()
            ->fromSub(
                DB::table('accounts')
                    ->join('users', 'users.account_id', '=', 'accounts.id')
                    ->whereNotNull('users.tenant_id')
                    ->groupBy('accounts.id')
                    ->havingRaw('COUNT(DISTINCT users.tenant_id) > 1')
                    ->select('accounts.id'),
                'conflicts'
            )
            ->count();

        $unlinked = (int) DB::table('accounts')
            ->leftJoin('users', 'users.account_id', '=', 'accounts.id')
            ->whereNull('users.id')
            ->count('accounts.id');

        return [
            'tenant_found' => true,
            'tenant_slug' => $tenantSlug,
            'account_tenant_column_exists' => $columnExists,
            'total_accounts' => $totalAccounts,
            'accounts_missing_tenant_assignment' => $missing,
            'accounts_linked_to_target_tenant_users' => $linkedTarget,
            'accounts_linked_to_other_tenant_users' => $linkedOther,
            'accounts_linked_to_multiple_tenants' => $multipleTenants,
            'unlinked_accounts' => $unlinked,
            'backfill_safe' => $columnExists
                && $linkedOther === 0
                && $multipleTenants === 0
                && $unlinked === 0
                && $linkedTarget === $totalAccounts,
        ];
    }
}
