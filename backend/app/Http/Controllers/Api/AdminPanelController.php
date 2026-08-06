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

        $page = $query->paginate(100);
        $page->through(fn (AuditLog $log): array => [
            'id' => $log->id,
            'actor_id' => $log->actor_id,
            'action' => $log->action,
            'subject_type' => $log->subject_type,
            'subject_id' => $log->subject_id,
            'request_id' => $log->request_id,
            'created_at' => $log->created_at?->toISOString(),
        ]);

        return response()->json($page);
    }

    public function outbox(): JsonResponse
    {
        $page = OutboxMessage::query()->latest('id')->paginate(100);
        $page->through(fn (OutboxMessage $message): array => [
            'uuid' => $message->uuid,
            'event_type' => $message->event_type,
            'aggregate_type' => $message->aggregate_type,
            'aggregate_id' => $message->aggregate_id,
            'attempts' => $message->attempts,
            'available_at' => $message->available_at?->toISOString(),
            'processed_at' => $message->processed_at?->toISOString(),
            'created_at' => $message->created_at?->toISOString(),
            'updated_at' => $message->updated_at?->toISOString(),
        ]);

        return response()->json($page);
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
