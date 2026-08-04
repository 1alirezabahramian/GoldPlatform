<?php

namespace App\Models;

use App\Enums\CustodyStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CustodyAsset extends Model
{
    protected $fillable = [
        'uuid', 'user_id', 'order_id', 'asset_type', 'external_product_id',
        'product_code', 'title', 'quantity', 'weight', 'fineness', 'barcode',
        'branch_code', 'status', 'acquired_at', 'ready_at', 'delivered_at', 'metadata',
    ];

    protected $casts = [
        'status' => CustodyStatus::class,
        'quantity' => 'decimal:8',
        'weight' => 'decimal:8',
        'fineness' => 'decimal:4',
        'acquired_at' => 'datetime',
        'ready_at' => 'datetime',
        'delivered_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $asset): void {
            $asset->uuid ??= (string) Str::uuid();
            $asset->status ??= CustodyStatus::InCustody;
            $asset->acquired_at ??= now();
        });
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function deliveryRequests(): HasMany { return $this->hasMany(DeliveryRequest::class); }
}
