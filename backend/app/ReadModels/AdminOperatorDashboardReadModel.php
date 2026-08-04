<?php

namespace App\ReadModels;

use App\Models\AuditLog;
use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Models\OutboxMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class AdminOperatorDashboardReadModel
{
    /** @return array<string, mixed> */
    public function adminSnapshot(): array
    {
        return [
            'orders' => [
                'total' => Order::query()->count(),
                'actionable' => Order::query()->whereIn('status', ['pending', 'approved', 'executing', 'settling'])->count(),
                'failed' => Order::query()->where('status', 'failed')->count(),
            ],
            'deliveries' => [
                'total' => DeliveryRequest::query()->count(),
                'actionable' => DeliveryRequest::query()->whereIn('status', ['requested', 'approved', 'ready'])->count(),
                'ready' => DeliveryRequest::query()->where('status', 'ready')->count(),
            ],
            'operations' => [
                'failed_jobs' => Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : null,
                'outbox_messages' => OutboxMessage::query()->count(),
                'latest_audit_at' => AuditLog::query()->max('created_at'),
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function operatorSnapshot(): array
    {
        return [
            'task_counts' => [
                'orders' => Order::query()->whereIn('status', ['pending', 'approved', 'executing', 'settling'])->count(),
                'deliveries' => DeliveryRequest::query()->whereIn('status', ['requested', 'approved', 'ready'])->count(),
                'deliveries_ready' => DeliveryRequest::query()->where('status', 'ready')->count(),
            ],
            'recent_orders' => Order::query()
                ->whereIn('status', ['pending', 'approved', 'executing', 'settling'])
                ->oldest('id')
                ->limit(10)
                ->get(['id', 'type', 'asset_type', 'asset_quantity', 'asset_unit', 'status', 'created_at'])
                ->map(fn (Order $order): array => [
                    'id' => $order->id,
                    'type' => $order->type,
                    'asset_type' => $order->asset_type?->value ?? $order->getRawOriginal('asset_type'),
                    'quantity' => $order->asset_quantity,
                    'unit' => $order->asset_unit,
                    'status' => $order->status?->value ?? $order->getRawOriginal('status'),
                    'created_at' => $order->created_at?->toIso8601String(),
                ])->all(),
            'recent_deliveries' => DeliveryRequest::query()
                ->whereIn('status', ['requested', 'approved', 'ready'])
                ->oldest('id')
                ->limit(10)
                ->get(['id', 'uuid', 'branch_code', 'requested_for', 'status', 'created_at'])
                ->map(fn (DeliveryRequest $delivery): array => [
                    'reference' => $delivery->uuid,
                    'branch_code' => $delivery->branch_code,
                    'requested_for' => $delivery->requested_for?->toIso8601String(),
                    'status' => $delivery->status?->value ?? $delivery->getRawOriginal('status'),
                    'created_at' => $delivery->created_at?->toIso8601String(),
                ])->all(),
        ];
    }
}
