<?php

namespace App\Services;

use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Models\User;
use App\Support\CustomerReadPresenter;

final class CustomerDashboardReadModel
{
    public function __construct(
        private readonly CustomerReadPresenter $resources,
    ) {}

    /**
     * Build only GoldPlatform-owned operational dashboard data.
     *
     * Money, gold, coin and currency balances are intentionally excluded.
     * Those balances must be resolved from Kimia by the dedicated customer
     * balance boundary before they can be exposed publicly.
     *
     * @return array<string, mixed>
     */
    public function for(User $user): array
    {
        $terminalOrders = ['completed', 'rejected', 'expired', 'cancelled', 'failed'];

        $recentOrders = Order::query()
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $recentCustodies = CustodyAsset::query()
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $recentDeliveries = DeliveryRequest::query()
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $activities = collect()
            ->concat($recentOrders->map(fn (Order $order) => [
                'event_type' => 'order_status',
                'status' => $order->status?->value,
                'occurred_at' => $order->updated_at?->toIso8601String(),
                'resource' => $this->resources->order($order),
            ]))
            ->concat($recentCustodies->map(fn (CustodyAsset $custody) => [
                'event_type' => 'custody_status',
                'status' => $custody->status?->value,
                'occurred_at' => $custody->updated_at?->toIso8601String(),
                'resource' => $this->resources->custody($custody),
            ]))
            ->concat($recentDeliveries->map(fn (DeliveryRequest $delivery) => [
                'event_type' => 'delivery_status',
                'status' => $delivery->status?->value,
                'occurred_at' => $delivery->updated_at?->toIso8601String(),
                'resource' => $this->resources->delivery($delivery),
            ]))
            ->sortByDesc('occurred_at')
            ->take(8)
            ->values();

        return [
            'summary' => [
                'active_orders' => Order::query()
                    ->where('user_id', $user->id)
                    ->whereNotIn('status', $terminalOrders)
                    ->count(),
                'custodies' => CustodyAsset::query()->where('user_id', $user->id)->count(),
                'delivery_requests' => DeliveryRequest::query()->where('user_id', $user->id)->count(),
                'ready_deliveries' => DeliveryRequest::query()
                    ->where('user_id', $user->id)
                    ->where('status', 'ready')
                    ->count(),
            ],
            'highlights' => [
                'active_orders' => $recentOrders
                    ->reject(fn (Order $order) => in_array($order->status?->value, $terminalOrders, true))
                    ->take(3)
                    ->map(fn (Order $order) => $this->resources->order($order))
                    ->values()
                    ->all(),
                'ready_deliveries' => $recentDeliveries
                    ->filter(fn (DeliveryRequest $delivery) => $delivery->status?->value === 'ready')
                    ->take(3)
                    ->map(fn (DeliveryRequest $delivery) => $this->resources->delivery($delivery))
                    ->values()
                    ->all(),
            ],
            'recent_activity' => $activities->all(),
        ];
    }
}
