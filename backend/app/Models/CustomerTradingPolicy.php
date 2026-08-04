<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerTradingPolicy extends Model
{
    protected $fillable = [
        'user_group_id', 'requires_available_balance', 'allow_negative_balance',
        'asset_lock_minutes', 'max_gold_weight', 'max_coin_quantity',
        'max_money_amount', 'credit_limit', 'min_order_amount', 'max_order_amount',
        'max_delivery_items', 'is_active', 'metadata',
    ];

    protected $casts = [
        'requires_available_balance' => 'boolean',
        'allow_negative_balance' => 'boolean',
        'asset_lock_minutes' => 'integer',
        'max_gold_weight' => 'decimal:8',
        'max_coin_quantity' => 'integer',
        'max_money_amount' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_order_amount' => 'decimal:2',
        'max_delivery_items' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }
}
