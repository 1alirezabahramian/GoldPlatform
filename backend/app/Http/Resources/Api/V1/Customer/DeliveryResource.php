<?php

namespace App\Http\Resources\Api\V1\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class DeliveryResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'reference' => $this->uuid,
            'custody_reference' => $this->relationLoaded('custodyAsset') ? $this->custodyAsset?->uuid : null,
            'branch_code' => $this->branch_code,
            'requested_for' => $this->requested_for?->toIso8601String(),
            'status' => $this->status?->value,
            'status_reason' => $this->status_reason,
            'approved_at' => $this->approved_at?->toIso8601String(),
            'ready_at' => $this->ready_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'rejected_at' => $this->rejected_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
