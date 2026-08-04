<?php

namespace App\ReadModels;

use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Models\OutboxMessage;
use App\Models\Settlement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class BackofficeOperationalDashboard
{
    /** @return array<string, mixed> */
    public function admin(): array
    {
        return [
            'summary' => [
                'open_orders' => $this->count(Order::query()->whereIn('status', ['pending', 'approved', 'executing', 'settling'])),
                'active_deliveries' => $this->count(DeliveryRequest::query()->whereIn('status', ['requested', 'approved', 'ready'])),
                'failed_settlements' => $this->count(Settlement::query()->where('status', 'failed')),
                'custody_items' => $this->count(CustodyAsset::query()->whereNotIn('status', ['delivered', 'cancelled'])),
                'pending_outbox' => $this->pendingOutboxCount(),
            ],
            'queues' => [
                'orders' => $this->orderPreview(8),
                'deliveries' => $this->deliveryPreview(8),
            ],
            'financial_metrics_supported' => false,
        ];
    }

    /** @return array<string, mixed> */
    public function operator(): array
    {
        return [
            'summary' => [
                'pending_orders' => $this->count(Order::query()->where('status', 'pending')),
                'approved_orders' => $this->count(Order::query()->where('status', 'approved')),
                'requested_deliveries' => $this->count(DeliveryRequest::query()->where('status', 'requested')),
                'ready_deliveries' => $this->count(DeliveryRequest::query()->where('status', 'ready')),
            ],
            'queues' => [
                'orders' => $this->orderPreview(12),
                'deliveries' => $this->deliveryPreview(12),
            ],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function orderPreview(int $limit): array
    {
        try {
            return Order::query()
                ->whereIn('status', ['pending', 'approved', 'executing', 'settling'])
                ->oldest('id')
                ->limit($limit)
                ->get(['id', 'status', 'created_at'])
                ->map(fn (Order $order) => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'created_at' => $order->created_at?->toIso8601String(),
                ])->all();
        } catch (Throwable) {
            return [];
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function deliveryPreview(int $limit): array
    {
        try {
            return DeliveryRequest::query()
                ->whereIn('status', ['requested', 'approved', 'ready'])
                ->oldest('id')
                ->limit($limit)
                ->get(['id', 'status', 'created_at'])
                ->map(fn (DeliveryRequest $delivery) => [
                    'id' => $delivery->id,
                    'status' => $delivery->status,
                    'created_at' => $delivery->created_at?->toIso8601String(),
                ])->all();
        } catch (Throwable) {
            return [];
        }
    }

    private function pendingOutboxCount(): ?int
    {
        if (! Schema::hasTable('outbox_messages') || ! Schema::hasColumn('outbox_messages', 'processed_at')) {
            return null;
        }

        return $this->count(OutboxMessage::query()->whereNull('processed_at'));
    }

    private function count(Builder $query): ?int
    {
        try {
            return $query->count();
        } catch (Throwable) {
            return null;
        }
    }
}
