<?php

namespace App\Models;

use App\Enums\DeliveryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DeliveryRequest extends Model
{
    protected $fillable = [
        'uuid', 'custody_asset_id', 'user_id', 'branch_code', 'requested_for',
        'status', 'status_reason', 'approved_by', 'delivered_by', 'receiver_name',
        'receiver_identifier', 'approved_at', 'ready_at', 'delivered_at',
        'rejected_at', 'cancelled_at', 'metadata',
    ];

    protected $casts = [
        'status' => DeliveryStatus::class,
        'requested_for' => 'datetime',
        'approved_at' => 'datetime',
        'ready_at' => 'datetime',
        'delivered_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $request): void {
            $request->uuid ??= (string) Str::uuid();
            $request->status ??= DeliveryStatus::Requested;
        });
    }

    public function custodyAsset(): BelongsTo { return $this->belongsTo(CustodyAsset::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
