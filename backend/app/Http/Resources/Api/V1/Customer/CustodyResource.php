<?php

namespace App\Http\Resources\Api\V1\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CustodyResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'reference' => $this->uuid,
            'asset_type' => $this->asset_type,
            'title' => $this->title,
            'quantity' => $this->quantity === null ? null : (string) $this->quantity,
            'weight' => $this->weight === null ? null : (string) $this->weight,
            'fineness' => $this->fineness === null ? null : (string) $this->fineness,
            'branch_code' => $this->branch_code,
            'status' => $this->status?->value,
            'acquired_at' => $this->acquired_at?->toIso8601String(),
            'ready_at' => $this->ready_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
        ];
    }
}
