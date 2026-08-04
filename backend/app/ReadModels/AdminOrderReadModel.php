<?php

namespace App\ReadModels;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

final class AdminOrderReadModel
{
    public function index(Request $request): array
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', 'max:40'],
            'type' => ['nullable', 'string', 'max:40'],
            'asset_type' => ['nullable', 'string', 'max:40'],
            'user_id' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Order::query()
            ->select([
                'id', 'user_id', 'type', 'asset_type', 'external_asset_id',
                'asset_quantity', 'asset_unit', 'status', 'gold_weight',
                'gold_price', 'commission', 'total_price', 'expires_at',
                'approved_at', 'executing_at', 'settling_at', 'completed_at',
                'rejected_at', 'cancelled_at', 'failed_at', 'expired_at',
                'status_reason', 'state_version', 'created_at', 'updated_at',
            ])
            ->withCount(['trades', 'settlements'])
            ->latest('id');

        foreach (['status', 'type', 'asset_type', 'user_id'] as $filter) {
            if (array_key_exists($filter, $validated)) {
                $query->where($filter, $validated[$filter]);
            }
        }

        return $this->presentPage($query->paginate((int) ($validated['per_page'] ?? 25)));
    }

    public function show(Order $order): array
    {
        $order->load([
            'trades:id,order_id,trade_no,quantity,unit_price,total_amount,status,executed_at,created_at',
            'settlements:id,uuid,order_id,trade_id,status,asset_type,amount,processing_started_at,completed_at,failed_at,created_at',
        ]);

        return [
            'order' => $this->presentOrder($order),
            'timeline' => $this->timeline($order),
            'trades' => $order->trades->map(fn ($trade) => [
                'trade_no' => $trade->trade_no,
                'quantity' => $trade->quantity,
                'unit_price' => $trade->unit_price,
                'total_amount' => $trade->total_amount,
                'status' => $trade->status,
                'executed_at' => $trade->executed_at?->toIso8601String(),
                'created_at' => $trade->created_at?->toIso8601String(),
            ])->values()->all(),
            'settlements' => $order->settlements->map(fn ($settlement) => [
                'reference' => $settlement->uuid,
                'trade_id' => $settlement->trade_id,
                'status' => $settlement->status?->value ?? $settlement->status,
                'asset_type' => $settlement->asset_type,
                'amount' => $settlement->amount,
                'processing_started_at' => $settlement->processing_started_at?->toIso8601String(),
                'completed_at' => $settlement->completed_at?->toIso8601String(),
                'failed_at' => $settlement->failed_at?->toIso8601String(),
                'created_at' => $settlement->created_at?->toIso8601String(),
            ])->values()->all(),
        ];
    }

    private function presentPage(LengthAwarePaginator $paginator): array
    {
        return [
            'items' => collect($paginator->items())->map(fn (Order $order) => $this->presentOrder($order))->values()->all(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ];
    }

    private function presentOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'type' => $order->type,
            'asset_type' => $order->asset_type?->value ?? $order->asset_type,
            'external_asset_id' => $order->external_asset_id,
            'asset_quantity' => $order->asset_quantity,
            'asset_unit' => $order->asset_unit,
            'status' => $order->status?->value ?? $order->status,
            'gold_weight' => $order->gold_weight,
            'gold_price' => $order->gold_price,
            'commission' => $order->commission,
            'total_price' => $order->total_price,
            'status_reason' => $order->status_reason,
            'state_version' => $order->state_version,
            'trades_count' => $order->trades_count ?? $order->trades->count(),
            'settlements_count' => $order->settlements_count ?? $order->settlements->count(),
            'created_at' => $order->created_at?->toIso8601String(),
            'updated_at' => $order->updated_at?->toIso8601String(),
            'expires_at' => $order->expires_at?->toIso8601String(),
        ];
    }

    private function timeline(Order $order): array
    {
        $events = [
            'created' => $order->created_at,
            'approved' => $order->approved_at,
            'executing' => $order->executing_at,
            'settling' => $order->settling_at,
            'completed' => $order->completed_at,
            'rejected' => $order->rejected_at,
            'cancelled' => $order->cancelled_at,
            'failed' => $order->failed_at,
            'expired' => $order->expired_at,
        ];

        return collect($events)
            ->filter()
            ->map(fn ($at, string $event) => ['event' => $event, 'at' => $at->toIso8601String()])
            ->sortBy('at')
            ->values()
            ->all();
    }
}
