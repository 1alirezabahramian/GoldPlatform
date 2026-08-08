<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PILOT_SLUG = 'khalifeh-coin';

    public function up(): void
    {
        $tenantId = DB::table('tenants')
            ->where('slug', self::PILOT_SLUG)
            ->value('id');

        if ($tenantId === null) {
            throw new \RuntimeException(
                'Pilot tenant khalifeh-coin is required before account tenancy backfill.'
            );
        }

        $conflictingAssignments = DB::table('accounts')
            ->whereNotNull('tenant_id')
            ->where('tenant_id', '!=', $tenantId)
            ->exists();

        if ($conflictingAssignments) {
            throw new \RuntimeException(
                'Account tenancy backfill refused because conflicting tenant assignments already exist.'
            );
        }

        DB::table('accounts')
            ->whereNull('tenant_id')
            ->update(['tenant_id' => $tenantId]);
    }

    public function down(): void
    {
        $tenantId = DB::table('tenants')
            ->where('slug', self::PILOT_SLUG)
            ->value('id');

        if ($tenantId === null) {
            return;
        }

        DB::table('accounts')
            ->where('tenant_id', $tenantId)
            ->update(['tenant_id' => null]);
    }
};
