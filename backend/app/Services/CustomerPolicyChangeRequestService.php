<?php

namespace App\Services;

use App\Enums\CustomerPolicyChangeStatus;
use App\Models\CustomerPolicyChangeRequest;
use App\Models\CustomerTradingPolicy;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

final class CustomerPolicyChangeRequestService
{
    public const ALLOWED_FIELDS = [
        'requires_available_balance', 'allow_negative_balance', 'asset_lock_minutes',
        'max_gold_weight', 'max_coin_quantity', 'max_money_amount', 'credit_limit',
        'min_order_amount', 'max_order_amount', 'max_delivery_items', 'is_active',
    ];

    public function createDraft(CustomerTradingPolicy $policy, array $changes, string $reason, User $actor): CustomerPolicyChangeRequest
    {
        if ($changes === [] || array_diff(array_keys($changes), self::ALLOWED_FIELDS) !== []) {
            throw new DomainException('Unsupported or empty policy change set.');
        }

        return CustomerPolicyChangeRequest::query()->create([
            'customer_trading_policy_id' => $policy->id,
            'proposed_changes' => $changes,
            'reason' => $reason,
            'created_by' => $actor->id,
            'status' => CustomerPolicyChangeStatus::Draft,
        ]);
    }

    public function submit(CustomerPolicyChangeRequest $request, User $actor): CustomerPolicyChangeRequest
    {
        return DB::transaction(function () use ($request, $actor): CustomerPolicyChangeRequest {
            $locked = CustomerPolicyChangeRequest::query()->lockForUpdate()->findOrFail($request->id);
            $this->requireStatus($locked, CustomerPolicyChangeStatus::Draft);
            $locked->forceFill([
                'status' => CustomerPolicyChangeStatus::Submitted,
                'submitted_by' => $actor->id,
                'submitted_at' => now(),
            ])->save();
            return $locked->refresh();
        });
    }

    public function approve(CustomerPolicyChangeRequest $request, User $actor, ?string $note): CustomerPolicyChangeRequest
    {
        return $this->review($request, $actor, CustomerPolicyChangeStatus::Approved, $note);
    }

    public function reject(CustomerPolicyChangeRequest $request, User $actor, string $note): CustomerPolicyChangeRequest
    {
        return $this->review($request, $actor, CustomerPolicyChangeStatus::Rejected, $note);
    }

    private function review(CustomerPolicyChangeRequest $request, User $actor, CustomerPolicyChangeStatus $status, ?string $note): CustomerPolicyChangeRequest
    {
        return DB::transaction(function () use ($request, $actor, $status, $note): CustomerPolicyChangeRequest {
            $locked = CustomerPolicyChangeRequest::query()->lockForUpdate()->findOrFail($request->id);
            $this->requireStatus($locked, CustomerPolicyChangeStatus::Submitted);
            $locked->forceFill([
                'status' => $status,
                'reviewed_by' => $actor->id,
                'review_note' => $note,
                'reviewed_at' => now(),
            ])->save();
            return $locked->refresh();
        });
    }

    private function requireStatus(CustomerPolicyChangeRequest $request, CustomerPolicyChangeStatus $expected): void
    {
        if ($request->status !== $expected) {
            throw new DomainException("Policy change request must be {$expected->value}.");
        }
    }
}
