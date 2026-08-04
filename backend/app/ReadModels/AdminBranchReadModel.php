<?php

namespace App\ReadModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class AdminBranchReadModel
{
    public function overview(): array
    {
        $codes = collect();

        if (Schema::hasTable('custody_assets') && Schema::hasColumn('custody_assets', 'branch_code')) {
            $codes = $codes->merge(DB::table('custody_assets')->whereNotNull('branch_code')->distinct()->pluck('branch_code'));
        }

        if (Schema::hasTable('delivery_requests') && Schema::hasColumn('delivery_requests', 'branch_code')) {
            $codes = $codes->merge(DB::table('delivery_requests')->whereNotNull('branch_code')->distinct()->pluck('branch_code'));
        }

        $items = $codes->filter(fn ($code) => is_string($code) && trim($code) !== '')
            ->map(fn ($code) => trim($code))
            ->unique()
            ->sort()
            ->values()
            ->map(function (string $code): array {
                return [
                    'code' => $code,
                    'custody_assets_count' => Schema::hasTable('custody_assets')
                        ? DB::table('custody_assets')->where('branch_code', $code)->count()
                        : 0,
                    'delivery_requests_count' => Schema::hasTable('delivery_requests')
                        ? DB::table('delivery_requests')->where('branch_code', $code)->count()
                        : 0,
                ];
            })->all();

        return [
            'branch_entity_supported' => false,
            'branch_code_projection_supported' => true,
            'tenant_scope_supported' => false,
            'user_branch_assignment_supported' => false,
            'inventory_branch_scope_supported' => false,
            'items' => $items,
        ];
    }
}
