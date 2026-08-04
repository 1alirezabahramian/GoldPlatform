<?php

namespace App\ReadModels;

use App\Models\DeliveryRequest;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

final class OperatorQueueReadModel
{
    private const ORDER_STATUSES = ['pending', 'approved', 'executing', 'settling'];
    private const DELIVERY_STATUSES = ['requested', 'approved', 'ready'];

    public function orders(Request $request): LengthAwarePaginator
    {
        $query = Order::query()->whereIn('status', self::ORDER_STATUSES);
        $this->filterStatus($query, $request, self::ORDER_STATUSES);

        return $query->oldest('id')->paginate($this->perPage($request))->through(fn (Order $order) => [
            'id' => $order->id,
            'type' => $order->type,
            'asset_type' => $order->asset_type?->value ?? (string) $order->asset_type,
            'quantity' => $order->asset_quantity === null ? null : (string) $order->asset_quantity,
            'unit' => $order->asset_unit,
            'status' => $order->status?->value ?? (string) $order->status,
            'created_at' => $order->created_at?->toISOString(),
            'expires_at' => $order->expires_at?->toISOString(),
        ]);
    }

    public function deliveries(Request $request): LengthAwarePaginator
    {
        $query = DeliveryRequest::query()->whereIn('status', self::DELIVERY_STATUSES);
        $this->filterStatus($query, $request, self::DELIVERY_STATUSES);

        return $query->oldest('id')->paginate($this->perPage($request))->through(fn (DeliveryRequest $delivery) => [
            'id' => $delivery->id,
            'reference' => $delivery->uuid,
            'branch_code' => $delivery->branch_code,
            'requested_for' => $delivery->requested_for?->toISOString(),
            'status' => $delivery->status?->value ?? (string) $delivery->status,
            'created_at' => $delivery->created_at?->toISOString(),
        ]);
    }

    private function filterStatus($query, Request $request, array $allowed): void
    {
        if (!$request->filled('status')) return;
        $status = $request->string('status')->toString();
        abort_unless(in_array($status, $allowed, true), 422, 'Invalid queue status.');
        $query->where('status', $status);
    }

    private function perPage(Request $request): int
    {
        return max(1, min(50, $request->integer('per_page', 25)));
    }
}
