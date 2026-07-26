<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    protected $fillable = [
        'order_id',
        'financial_transaction_id',
        'trade_no',
        'quantity',
        'unit_price',
        'total_amount',
        'status',
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function financialTransaction(): BelongsTo
    {
        return $this->belongsTo(FinancialTransaction::class);
    }
}