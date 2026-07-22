<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_account_id',
        'wallet_type',
        'type',
        'amount',
        'balance_after',
        'reference',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:6',
        'balance_after' => 'decimal:6',
    ];

    /**
     * Wallet Account
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(
            WalletAccount::class,
            'wallet_account_id'
        );
    }
}