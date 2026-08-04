<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CustomerTradingPolicy;
use App\Models\OutboxMessage;
use App\Services\AuditService;
use App\Services\OutboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPanelController extends Controller
{
    public function auditLogs(Request $request): JsonResponse
    {
        $query = AuditLog::query()->latest('id');
        if ($request->filled('action')) $query->where('action', $request->string('action'));
        if ($request->filled('request_id')) $query->where('request_id', $request->string('request_id'));
        return response()->json($query->paginate(100));
    }

    public function outbox(): JsonResponse
    {
        return response()->json(OutboxMessage::query()->latest('id')->paginate(100));
    }

    public function policies(): JsonResponse
    {
        return response()->json(CustomerTradingPolicy::query()->latest('id')->paginate(100));
    }

    public function updatePolicy(Request $request, CustomerTradingPolicy $policy, AuditService $audit, OutboxService $outbox): JsonResponse
    {
        $data = $request->validate([
            'requires_available_balance' => ['sometimes','boolean'],
            'allow_negative_balance' => ['sometimes','boolean'],
            'asset_lock_minutes' => ['nullable','integer','min:0'],
            'max_gold_weight' => ['nullable','numeric'],
            'max_coin_quantity' => ['nullable','integer','min:0'],
            'max_money_amount' => ['nullable','numeric'],
            'credit_limit' => ['nullable','numeric'],
            'min_order_amount' => ['nullable','numeric'],
            'max_order_amount' => ['nullable','numeric'],
            'max_delivery_items' => ['nullable','integer','min:0'],
            'is_active' => ['sometimes','boolean'],
        ]);

        $updated = DB::transaction(function () use ($request, $policy, $data, $audit, $outbox) {
            $before = $policy->toArray();
            $policy->fill($data)->save();
            $policy->refresh();
            $audit->record('customer_policy.updated', $policy, $before, $policy->toArray(), request: $request);
            $outbox->enqueue('customer_policy.updated', ['policy_id' => $policy->id], $policy);
            return $policy;
        });

        return response()->json($updated);
    }
}
