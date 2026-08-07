<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PILOT_SLUG = 'khalifeh-coin';

    public function up(): void
    {
        $tenant = DB::table('tenants')->where('slug', self::PILOT_SLUG)->first();

        if ($tenant === null) {
            $tenantId = DB::table('tenants')->insertGetId([
                'name' => 'طلا و سکه خلیفه',
                'slug' => self::PILOT_SLUG,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $tenantId = (int) $tenant->id;
        }

        DB::table('users')
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

        DB::table('users')
            ->where('tenant_id', $tenantId)
            ->update(['tenant_id' => null]);

        $hasDomains = DB::table('tenant_domains')->where('tenant_id', $tenantId)->exists();

        if (! $hasDomains) {
            DB::table('tenants')->where('id', $tenantId)->delete();
        }
    }
};