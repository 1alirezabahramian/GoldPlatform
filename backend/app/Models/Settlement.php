<?php

namespace App\Models;

use App\Enums\SettlementStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Settlement extends Model
{
    protected $fillable = [
        'uuid',
        'order_id',
        'trade_id',
        'financial_transaction_id',
        'status',
        'asset_type',
        'amount',
        'idempotency_key',
        'kimia_reference',
        'failure_reason',
        'metadata',
        'processing_started_at',
        'completed_at',
        'failed_at',
    ];

    protected $casts = [
        'status' => SettlementStatus::class,
        'amount' => 'decimal:8',
        'metadata' => 'array',
        'processing_started_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Settlement $settlement): void {
            if (blank($settlement->uuid)) {
                $settlement->uuid = (string) Str::uuid();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function trade(): BelongsTo
    {
        return $this->belongsTo(Trade::class);
    }

    public function financialTransaction(): BelongsTo
    {
        return $this->belongsTo(FinancialTransaction::class);
    }
}
