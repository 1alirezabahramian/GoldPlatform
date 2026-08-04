<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;

class OrderService
{
    public function create(array $data): Order
    {
        $goldWeight = (string) $data['gold_weight'];
        $goldPrice = (string) $data['gold_price'];
        $commission = (string) ($data['commission'] ?? 0);

        $goldAmount = bcmul($goldWeight, $goldPrice, 2);
        $totalPrice = bcadd($goldAmount, $commission, 2);

        return Order::create([
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'status' => OrderStatus::Pending,
            'gold_weight' => $goldWeight,
            'gold_price' => $goldPrice,
            'commission' => $commission,
            'total_price' => $totalPrice,
            'description' => $data['description'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
        ]);
    }
}
