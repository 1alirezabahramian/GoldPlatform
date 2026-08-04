<?php

namespace App\ReadModels;

use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

final class AdminCustodyDeliveryReadModel
{
    public function custodies(Request $request): array
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', 'max:50'],
            'asset_type' => ['nullable', 'string', 'max:50'],
            'branch_code' => ['nullable', 'string', 'max:50'],
            'user_id' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = CustodyAsset::query()
            ->select(['id', 'uuid', 'user_id', 'order_id', 'asset_type', 'external_product_id', 'product_code', 'title', 'quantity', 'weight', 'fineness', 'barcode', 'branch_code', 'status', 'acquired_at', 'ready_at', 'delivered_at', 'created_at', 'updated_at'])
            ->withCount('deliveryRequests')
            ->latest('id');

        foreach (['status', 'asset_type', 'branch_code', 'user_id'] as $field) {
            if (isset($validated[$field])) {
                $query->where($field, $validated[$field]);
            }
        }

        return $this->paginate($query->paginate((int) ($validated['per_page'] ?? 25)), fn (CustodyAsset $asset) => $this->presentCustody($asset));
    }

    public function custody(CustodyAsset $asset): array
    {
        $asset->load(['deliveryRequests' => fn ($query) => $query->select(['id', 'uuid', 'custody_asset_id', 'branch_code', 'requested_for', 'status', 'approved_at', 'ready_at', 'delivered_at', 'rejected_at', 'cancelled_at', 'created_at'])->latest('id')]);

        return [
            'custody' => $this->presentCustody($asset),
            'timeline' => $this->custodyTimeline($asset),
            'delivery_requests' => $asset->deliveryRequests->map(fn (DeliveryRequest $request) => $this->presentDelivery($request))->values()->all(),
        ];
    }

    public function deliveries(Request $request): array
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', 'max:50'],
            'branch_code' => ['nullable', 'string', 'max:50'],
            'user_id' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = DeliveryRequest::query()
            ->select(['id', 'uuid', 'custody_asset_id', 'user_id', 'branch_code', 'requested_for', 'status', 'approved_at', 'ready_at', 'delivered_at', 'rejected_at', 'cancelled_at', 'created_at', 'updated_at'])
            ->latest('id');

        foreach (['status', 'branch_code', 'user_id'] as $field) {
            if (isset($validated[$field])) {
                $query->where($field, $validated[$field]);
            }
        }

        return $this->paginate($query->paginate((int) ($validated['per_page'] ?? 25)), fn (DeliveryRequest $delivery) => $this->presentDelivery($delivery));
    }

    public function delivery(DeliveryRequest $delivery): array
    {
        $delivery->load(['custodyAsset' => fn ($query) => $query->select(['id', 'uuid', 'asset_type', 'product_code', 'title', 'quantity', 'weight', 'fineness', 'barcode', 'branch_code', 'status'])]);

        return [
            'delivery' => $this->presentDelivery($delivery),
            'timeline' => $this->deliveryTimeline($delivery),
            'custody' => $delivery->custodyAsset ? $this->presentCustody($delivery->custodyAsset) : null,
        ];
    }

    private function presentCustody(CustodyAsset $asset): array
    {
        return [
            'reference' => $asset->uuid,
            'user_id' => $asset->user_id,
            'order_id' => $asset->order_id,
            'asset_type' => $asset->asset_type,
            'external_product_id' => $asset->external_product_id,
            'product_code' => $asset->product_code,
            'title' => $asset->title,
            'quantity' => $asset->quantity,
            'weight' => $asset->weight,
            'fineness' => $asset->fineness,
            'barcode' => $asset->barcode,
            'branch_code' => $asset->branch_code,
            'status' => $asset->status?->value ?? $asset->status,
            'delivery_requests_count' => $asset->delivery_requests_count ?? $asset->deliveryRequests?->count(),
            'acquired_at' => $asset->acquired_at?->toIso8601String(),
            'ready_at' => $asset->ready_at?->toIso8601String(),
            'delivered_at' => $asset->delivered_at?->toIso8601String(),
            'created_at' => $asset->created_at?->toIso8601String(),
            'updated_at' => $asset->updated_at?->toIso8601String(),
        ];
    }

    private function presentDelivery(DeliveryRequest $delivery): array
    {
        return [
            'reference' => $delivery->uuid,
            'custody_asset_id' => $delivery->custody_asset_id,
            'user_id' => $delivery->user_id,
            'branch_code' => $delivery->branch_code,
            'requested_for' => $delivery->requested_for?->toIso8601String(),
            'status' => $delivery->status?->value ?? $delivery->status,
            'approved_at' => $delivery->approved_at?->toIso8601String(),
            'ready_at' => $delivery->ready_at?->toIso8601String(),
            'delivered_at' => $delivery->delivered_at?->toIso8601String(),
            'rejected_at' => $delivery->rejected_at?->toIso8601String(),
            'cancelled_at' => $delivery->cancelled_at?->toIso8601String(),
            'created_at' => $delivery->created_at?->toIso8601String(),
            'updated_at' => $delivery->updated_at?->toIso8601String(),
        ];
    }

    private function custodyTimeline(CustodyAsset $asset): array
    {
        return collect([
            'created' => $asset->created_at,
            'acquired' => $asset->acquired_at,
            'ready' => $asset->ready_at,
            'delivered' => $asset->delivered_at,
        ])->filter()->map(fn ($at, $event) => ['event' => $event, 'at' => $at->toIso8601String()])->values()->all();
    }

    private function deliveryTimeline(DeliveryRequest $delivery): array
    {
        return collect([
            'requested' => $delivery->created_at,
            'approved' => $delivery->approved_at,
            'ready' => $delivery->ready_at,
            'delivered' => $delivery->delivered_at,
            'rejected' => $delivery->rejected_at,
            'cancelled' => $delivery->cancelled_at,
        ])->filter()->map(fn ($at, $event) => ['event' => $event, 'at' => $at->toIso8601String()])->values()->all();
    }

    private function paginate(LengthAwarePaginator $paginator, callable $presenter): array
    {
        return [
            'items' => collect($paginator->items())->map($presenter)->values()->all(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ];
    }
}
