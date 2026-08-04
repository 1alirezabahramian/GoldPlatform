<?php

namespace App\Support;

use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\Models\Order;

final class CustomerReadPresenter
{
    public function order(Order $order): array
    {
        return [
            'type' => $order->type,
            'asset_type' => $order->asset_type?->value,
            'quantity' => $order->asset_quantity === null ? null : (string) $order->asset_quantity,
            'unit' => $order->asset_unit,
            'status' => $order->status?->value,
            'status_reason' => $order->status_reason,
            'expires_at' => $order->expires_at?->toIso8601String(),
            'created_at' => $order->created_at?->toIso8601String(),
            'updated_at' => $order->updated_at?->toIso8601String(),
        ];
    }

    public function custody(CustodyAsset $asset): array
    {
        return [
            'reference' => $asset->uuid,
            'asset_type' => $asset->asset_type,
            'title' => $asset->title,
            'quantity' => $asset->quantity === null ? null : (string) $asset->quantity,
            'weight' => $asset->weight === null ? null : (string) $asset->weight,
            'fineness' => $asset->fineness === null ? null : (string) $asset->fineness,
            'branch_code' => $asset->branch_code,
            'status' => $asset->status?->value,
            'acquired_at' => $asset->acquired_at?->toIso8601String(),
            'ready_at' => $asset->ready_at?->toIso8601String(),
            'delivered_at' => $asset->delivered_at?->toIso8601String(),
        ];
    }

    public function delivery(DeliveryRequest $delivery): array
    {
        return [
            'reference' => $delivery->uuid,
            'custody_reference' => $delivery->relationLoaded('custodyAsset') ? $delivery->custodyAsset?->uuid : null,
            'branch_code' => $delivery->branch_code,
            'requested_for' => $delivery->requested_for?->toIso8601String(),
            'status' => $delivery->status?->value,
            'status_reason' => $delivery->status_reason,
            'approved_at' => $delivery->approved_at?->toIso8601String(),
            'ready_at' => $delivery->ready_at?->toIso8601String(),
            'delivered_at' => $delivery->delivered_at?->toIso8601String(),
            'rejected_at' => $delivery->rejected_at?->toIso8601String(),
            'cancelled_at' => $delivery->cancelled_at?->toIso8601String(),
            'created_at' => $delivery->created_at?->toIso8601String(),
        ];
    }
}
