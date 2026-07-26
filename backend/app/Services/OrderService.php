<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function __construct(
        private TradeService $tradeService
    ) {}

    public function create(array $data): Order
    {
        $goldWeight = $data['gold_weight'];
        $goldPrice = $data['gold_price'];
        $commission = $data['commission'] ?? 0;

        $goldAmount = bcmul(
            $goldWeight,
            $goldPrice,
            2
        );

        $totalPrice = bcadd(
            $goldAmount,
            $commission,
            2
        );

        return Order::create([
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'status' => 'pending',
            'gold_weight' => $goldWeight,
            'gold_price' => $goldPrice,
            'commission' => $commission,
            'total_price' => $totalPrice,
            'description' => $data['description'] ?? null,
        ]);
    }
}