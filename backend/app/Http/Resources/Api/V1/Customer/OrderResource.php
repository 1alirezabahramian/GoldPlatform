<?php

namespace App\Http\Resources\Api\V1\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OrderResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->type,
            'asset_type' => $this->asset_type?->value,
            'quantity' => $this->asset_quantity === null ? null : (string) $this->asset_quantity,
            'unit' => $this->asset_unit,
            'status' => $this->status?->value,
            'status_reason' => $this->status_reason,
            'expires_at' => $this->expires_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
