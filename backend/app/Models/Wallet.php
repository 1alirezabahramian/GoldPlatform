<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    protected $fillable = [

        'user_id',

        'rial_balance',

        'gold_balance',

        'blocked_rial',

        'blocked_gold',

    ];

    protected $casts = [

        'rial_balance' => 'decimal:0',

        'gold_balance' => 'decimal:3',

        'blocked_rial' => 'decimal:0',

        'blocked_gold' => 'decimal:3',

    ];

    /**
     * Wallet Owner
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}