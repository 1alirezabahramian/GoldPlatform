<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletAccount extends Model
{
    protected $fillable = [
        'wallet_id',
        'code',
        'title',
        'balance',
        'blocked_balance',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:6',
        'blocked_balance' => 'decimal:6',
        'is_active' => 'boolean',
    ];

    /**
     * Parent Wallet
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Available Balance
     */
    public function getAvailableBalanceAttribute(): string
    {
        return bcsub(
            $this->balance,
            $this->blocked_balance,
            6
        );
    }
}