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

    public function updatePolicy(Request $request, CustomerTradingPolicy $policy): JsonResponse
    {
        return response()->json([
            'message' => 'Financial policy updates are disabled until their rules and Kimia authority boundaries are verified against accepted ground truth.',
            'code' => 'FINANCIAL_POLICY_GROUND_TRUTH_REQUIRED',
            'policy_id' => $policy->id,
        ], 503);
    }
}
