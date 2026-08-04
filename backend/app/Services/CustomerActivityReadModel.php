<?php

namespace App\Services;

use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Models\User;
use App\Support\CustomerReadPresenter;
use Illuminate\Support\Collection;

final class CustomerActivityReadModel
{
    private const EVENT_TYPES = ['order_status', 'custody_status', 'delivery_status'];

    public function __construct(private readonly CustomerReadPresenter $resources) {}

    /** @return array{items: array<int, array<string, mixed>>, pagination: array<string, int|bool>} */
    public function page(User $user, int $page = 1, int $perPage = 25, ?string $eventType = null): array
    {
        $page = max(1, $page);
        $perPage = min(50, max(1, $perPage));
        $offset = ($page - 1) * $perPage;
        $fetchLimit = $offset + $perPage;

        $sources = $eventType === null ? self::EVENT_TYPES : [$eventType];
        $activities = collect();
        $total = 0;

        if (in_array('order_status', $sources, true)) {
            $query = Order::query()->where('user_id', $user->id);
            $total += (clone $query)->count();
            $activities = $activities->concat(
                $query->latest('updated_at')->limit($fetchLimit)->get()
                    ->map(fn (Order $order) => $this->orderActivity($order))
            );
        }

        if (in_array('custody_status', $sources, true)) {
            $query = CustodyAsset::query()->where('user_id', $user->id);
            $total += (clone $query)->count();
            $activities = $activities->concat(
                $query->latest('updated_at')->limit($fetchLimit)->get()
                    ->map(fn (CustodyAsset $asset) => $this->custodyActivity($asset))
            );
        }

        if (in_array('delivery_status', $sources, true)) {
            $query = DeliveryRequest::query()
                ->with('custodyAsset:id,uuid')
                ->where('user_id', $user->id);
            $total += (clone $query)->count();
            $activities = $activities->concat(
                $query->latest('updated_at')->limit($fetchLimit)->get()
                    ->map(fn (DeliveryRequest $delivery) => $this->deliveryActivity($delivery))
            );
        }

        $items = $activities
            ->sortByDesc('occurred_at')
            ->slice($offset, $perPage)
            ->values()
            ->all();

        $lastPage = max(1, (int) ceil($total / $perPage));

        return [
            'items' => $items,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
                'has_more' => $page < $lastPage,
            ],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function recent(User $user, int $limit = 8): array
    {
        $limit = min(25, max(1, $limit));

        return $this->page($user, 1, $limit)['items'];
    }

    /** @return list<string> */
    public static function eventTypes(): array
    {
        return self::EVENT_TYPES;
    }

    /** @return array<string, mixed> */
    private function orderActivity(Order $order): array
    {
        return [
            'event_type' => 'order_status',
            'status' => $order->status?->value,
            'occurred_at' => $order->updated_at?->toIso8601String(),
            'resource' => $this->resources->order($order),
        ];
    }

    /** @return array<string, mixed> */
    private function custodyActivity(CustodyAsset $asset): array
    {
        return [
            'event_type' => 'custody_status',
            'status' => $asset->status?->value,
            'occurred_at' => $asset->updated_at?->toIso8601String(),
            'resource' => $this->resources->custody($asset),
        ];
    }

    /** @return array<string, mixed> */
    private function deliveryActivity(DeliveryRequest $delivery): array
    {
        return [
            'event_type' => 'delivery_status',
            'status' => $delivery->status?->value,
            'occurred_at' => $delivery->updated_at?->toIso8601String(),
            'resource' => $this->resources->delivery($delivery),
        ];
    }
}
