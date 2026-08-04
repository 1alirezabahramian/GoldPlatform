<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CustomerPolicyChangeRequest;
use App\Models\CustomerTradingPolicy;
use App\Services\AuditService;
use App\Services\CustomerPolicyChangeRequestService;
use App\Services\OutboxService;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerPolicyChangeRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:draft,submitted,approved,rejected,applied'],
            'policy_id' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = CustomerPolicyChangeRequest::query()
            ->select([
                'uuid', 'customer_trading_policy_id', 'proposed_changes', 'status', 'reason',
                'review_note', 'created_by', 'submitted_by', 'reviewed_by',
                'submitted_at', 'reviewed_at', 'applied_at', 'created_at',
            ])
            ->latest('id');

        if (isset($validated['status'])) $query->where('status', $validated['status']);
        if (isset($validated['policy_id'])) $query->where('customer_trading_policy_id', $validated['policy_id']);

        $paginator = $query->paginate((int) ($validated['per_page'] ?? 25));

        return AdminOperatorApiResponse::success($request, [
            'items' => collect($paginator->items())->map(fn (CustomerPolicyChangeRequest $item) => $this->present($item))->all(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(Request $request, CustomerPolicyChangeRequestService $service, AuditService $audit, OutboxService $outbox): JsonResponse
    {
        $data = $request->validate([
            'customer_trading_policy_id' => ['required', 'integer', 'exists:customer_trading_policies,id'],
            'proposed_changes' => ['required', 'array', 'min:1'],
            'proposed_changes.*' => ['nullable'],
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        $policy = CustomerTradingPolicy::query()->findOrFail($data['customer_trading_policy_id']);
        $change = $service->createDraft($policy, $data['proposed_changes'], $data['reason'], $request->user());
        $audit->record('customer_policy_change.drafted', $change, null, $this->present($change), request: $request);
        $outbox->enqueue('customer_policy_change.drafted', ['request_uuid' => $change->uuid], $change);

        return AdminOperatorApiResponse::success($request, $this->present($change));
    }

    public function submit(Request $request, CustomerPolicyChangeRequest $changeRequest, CustomerPolicyChangeRequestService $service, AuditService $audit, OutboxService $outbox): JsonResponse
    {
        $before = $this->present($changeRequest);
        $updated = $service->submit($changeRequest, $request->user());
        $audit->record('customer_policy_change.submitted', $updated, $before, $this->present($updated), request: $request);
        $outbox->enqueue('customer_policy_change.submitted', ['request_uuid' => $updated->uuid], $updated);
        return AdminOperatorApiResponse::success($request, $this->present($updated));
    }

    public function approve(Request $request, CustomerPolicyChangeRequest $changeRequest, CustomerPolicyChangeRequestService $service, AuditService $audit, OutboxService $outbox): JsonResponse
    {
        $data = $request->validate(['review_note' => ['nullable', 'string', 'max:2000']]);
        $before = $this->present($changeRequest);
        $updated = $service->approve($changeRequest, $request->user(), $data['review_note'] ?? null);
        $audit->record('customer_policy_change.approved', $updated, $before, $this->present($updated), request: $request);
        $outbox->enqueue('customer_policy_change.approved', ['request_uuid' => $updated->uuid], $updated);
        return AdminOperatorApiResponse::success($request, $this->present($updated));
    }

    public function reject(Request $request, CustomerPolicyChangeRequest $changeRequest, CustomerPolicyChangeRequestService $service, AuditService $audit, OutboxService $outbox): JsonResponse
    {
        $data = $request->validate(['review_note' => ['required', 'string', 'max:2000']]);
        $before = $this->present($changeRequest);
        $updated = $service->reject($changeRequest, $request->user(), $data['review_note']);
        $audit->record('customer_policy_change.rejected', $updated, $before, $this->present($updated), request: $request);
        $outbox->enqueue('customer_policy_change.rejected', ['request_uuid' => $updated->uuid], $updated);
        return AdminOperatorApiResponse::success($request, $this->present($updated));
    }

    private function present(CustomerPolicyChangeRequest $item): array
    {
        return [
            'reference' => $item->uuid,
            'policy_id' => $item->customer_trading_policy_id,
            'proposed_changes' => $item->proposed_changes,
            'status' => $item->status->value,
            'reason' => $item->reason,
            'review_note' => $item->review_note,
            'created_by' => $item->created_by,
            'submitted_by' => $item->submitted_by,
            'reviewed_by' => $item->reviewed_by,
            'submitted_at' => $item->submitted_at?->toIso8601String(),
            'reviewed_at' => $item->reviewed_at?->toIso8601String(),
            'applied_at' => $item->applied_at?->toIso8601String(),
            'created_at' => $item->created_at?->toIso8601String(),
        ];
    }
}
