<?php

namespace App\Models;

use App\Enums\AssetType;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'asset_type',
        'external_asset_id',
        'asset_quantity',
        'asset_unit',
        'status',
        'gold_weight',
        'gold_price',
        'commission',
        'total_price',
        'description',
        'expires_at',
        'approved_at',
        'executing_at',
        'settling_at',
        'completed_at',
        'rejected_at',
        'cancelled_at',
        'failed_at',
        'expired_at',
        'status_reason',
        'state_version',
    ];

    protected $casts = [
        'asset_type' => AssetType::class,
        'external_asset_id' => 'integer',
        'asset_quantity' => 'decimal:8',
        'status' => OrderStatus::class,
        'gold_weight' => 'decimal:3',
        'gold_price' => 'decimal:0',
        'commission' => 'decimal:0',
        'total_price' => 'decimal:0',
        'expires_at' => 'datetime',
        'approved_at' => 'datetime',
        'executing_at' => 'datetime',
        'settling_at' => 'datetime',
        'completed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'failed_at' => 'datetime',
        'expired_at' => 'datetime',
        'state_version' => 'integer',
    ];

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    public function balanceReservations(): HasMany
    {
        return $this->hasMany(BalanceReservation::class);
    }
}
