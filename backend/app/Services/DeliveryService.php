<?php

namespace App\Services;

use App\Enums\CustodyStatus;
use App\Enums\DeliveryStatus;
use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use LogicException;

class DeliveryService
{
    public function __construct(private CustodyService $custody) {}

    public function request(CustodyAsset $asset, User $user, array $data = []): DeliveryRequest
    {
        return DB::transaction(function () use ($asset, $user, $data): DeliveryRequest {
            $locked = CustodyAsset::query()->lockForUpdate()->findOrFail($asset->id);
            if ($locked->user_id !== $user->id) {
                throw new LogicException('Custody ownership mismatch.');
            }
            if ($locked->status->isTerminal()) {
                throw new LogicException('Terminal custody cannot be delivered.');
            }

            $existing = DeliveryRequest::query()
                ->where('custody_asset_id', $locked->id)
                ->whereIn('status', [
                    DeliveryStatus::Requested->value,
                    DeliveryStatus::Approved->value,
                    DeliveryStatus::Ready->value,
                ])->first();
            if ($existing) {
                return $existing;
            }

            $request = DeliveryRequest::query()->create([
                'custody_asset_id' => $locked->id,
                'user_id' => $user->id,
                'branch_code' => $data['branch_code'] ?? $locked->branch_code,
                'requested_for' => $data['requested_for'] ?? null,
                'status' => DeliveryStatus::Requested,
                'metadata' => $data['metadata'] ?? null,
            ]);
            $this->custody->markDeliveryRequested($locked);
            return $request;
        });
    }

    public function approve(DeliveryRequest $request, User $operator): DeliveryRequest
    {
        return $this->transition($request, DeliveryStatus::Approved, [DeliveryStatus::Requested], [
            'approved_by' => $operator->id,
            'approved_at' => now(),
        ]);
    }

    public function markReady(DeliveryRequest $request): DeliveryRequest
    {
        $result = $this->transition($request, DeliveryStatus::Ready, [DeliveryStatus::Approved], [
            'ready_at' => now(),
        ]);
        $this->custody->markReady($result->custodyAsset);
        return $result;
    }

    public function deliver(DeliveryRequest $request, User $operator, string $receiverName, string $receiverIdentifier): DeliveryRequest
    {
        if (trim($receiverName) === '' || trim($receiverIdentifier) === '') {
            throw new LogicException('Receiver identity is required.');
        }
        $result = $this->transition($request, DeliveryStatus::Delivered, [DeliveryStatus::Ready], [
            'delivered_by' => $operator->id,
            'receiver_name' => $receiverName,
            'receiver_identifier' => $receiverIdentifier,
            'delivered_at' => now(),
        ]);
        $this->custody->markDelivered($result->custodyAsset);
        return $result;
    }

    public function reject(DeliveryRequest $request, string $reason): DeliveryRequest
    {
        if (trim($reason) === '') {
            throw new LogicException('Rejection reason is required.');
        }
        return $this->transition($request, DeliveryStatus::Rejected, [DeliveryStatus::Requested], [
            'status_reason' => $reason,
            'rejected_at' => now(),
        ]);
    }

    private function transition(DeliveryRequest $request, DeliveryStatus $to, array $from, array $extra = []): DeliveryRequest
    {
        return DB::transaction(function () use ($request, $to, $from, $extra): DeliveryRequest {
            $locked = DeliveryRequest::query()->lockForUpdate()->findOrFail($request->id);
            if ($locked->status->isTerminal()) {
                throw new LogicException("Terminal delivery cannot transition from {$locked->status->value}.");
            }
            if ($locked->status === $to) {
                return $locked;
            }
            if (! in_array($locked->status, $from, true)) {
                throw new LogicException("Invalid delivery transition {$locked->status->value} -> {$to->value}.");
            }
            $locked->forceFill($extra + ['status' => $to])->save();
            return $locked->refresh();
        });
    }
}
