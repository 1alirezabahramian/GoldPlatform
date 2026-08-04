<?php

namespace App\Services;

use App\Models\CustomerTradingPolicy;
use App\Models\User;
use App\Support\Decimal;
use LogicException;

class TradingPolicyService
{
    public function policyFor(User $user): CustomerTradingPolicy
    {
        $policy = CustomerTradingPolicy::query()
            ->where('user_group_id', $user->group_id)
            ->where('is_active', true)
            ->first();

        if (! $policy) {
            throw new LogicException('No active trading policy is configured for this customer group.');
        }

        return $policy;
    }

    public function assertOrderAllowed(User $user, array $order): CustomerTradingPolicy
    {
        $policy = $this->policyFor($user);
        $amount = isset($order['total_amount']) ? Decimal::normalize((string) $order['total_amount'], 2) : null;

        if ($amount !== null && $policy->min_order_amount !== null && Decimal::compare($amount, $policy->min_order_amount, 2) < 0) {
            throw new LogicException('Order amount is below the configured minimum.');
        }
        if ($amount !== null && $policy->max_order_amount !== null && Decimal::compare($amount, $policy->max_order_amount, 2) > 0) {
            throw new LogicException('Order amount exceeds the configured maximum.');
        }
        if (($order['asset_type'] ?? null) === 'gold' && $policy->max_gold_weight !== null
            && Decimal::compare((string) ($order['quantity'] ?? '0'), $policy->max_gold_weight, 8) > 0) {
            throw new LogicException('Gold weight exceeds the configured group limit.');
        }
        if (($order['asset_type'] ?? null) === 'coin' && $policy->max_coin_quantity !== null
            && (int) ($order['quantity'] ?? 0) > $policy->max_coin_quantity) {
            throw new LogicException('Coin quantity exceeds the configured group limit.');
        }
        if ($amount !== null && $policy->max_money_amount !== null && Decimal::compare($amount, $policy->max_money_amount, 2) > 0) {
            throw new LogicException('Money amount exceeds the configured group limit.');
        }

        return $policy;
    }

    public function assertDeliveryAllowed(User $user, int $itemCount): CustomerTradingPolicy
    {
        $policy = $this->policyFor($user);
        if ($policy->max_delivery_items !== null && $itemCount > $policy->max_delivery_items) {
            throw new LogicException('Delivery item count exceeds the configured group limit.');
        }
        return $policy;
    }
}
