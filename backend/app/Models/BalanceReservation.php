<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BalanceReservation extends Model
{
    protected $fillable = [
        'uuid',
        'wallet_account_id',
        'order_id',
        'amount',
        'status',
        'idempotency_key',
        'released_at',
        'consumed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'released_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (BalanceReservation $reservation): void {
            $reservation->uuid ??= (string) Str::uuid();
        });
    }

    public function walletAccount(): BelongsTo
    {
        return $this->belongsTo(WalletAccount::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
