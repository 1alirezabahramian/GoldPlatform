<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CustomerTradingPolicy;
use App\Models\OutboxMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPanelController extends Controller
{
    public function auditLogs(Request $request): JsonResponse
    {
        $query = AuditLog::query()->latest('id');
        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }
        if ($request->filled('request_id')) {
            $query->where('request_id', $request->string('request_id'));
        }
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

    public function updatePolicy(Request $request, CustomerTradingPolicy $policy): JsonResponse
    {
        $data = $request->validate([
            'is_active' => ['sometimes','boolean'],
            'max_gold_weight' => ['nullable','numeric'],
            'max_coin_quantity' => ['nullable','numeric'],
            'max_order_money' => ['nullable','numeric'],
            'allow_negative_balance' => ['sometimes','boolean'],
            'holding_period_minutes' => ['nullable','integer','min:0'],
        ]);
        $policy->fill($data)->save();
        return response()->json($policy->refresh());
    }
}
