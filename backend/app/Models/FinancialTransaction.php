<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialTransaction extends Model
{
    protected $fillable = [
        'uuid',
        'type',
        'status',
        'reference_type',
        'reference_id',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function trade(): BelongsTo
    {
        return $this->belongsTo(
            Trade::class,
            'reference_id'
        );
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(
            LedgerEntry::class,
            'financial_transaction_id'
        );
    }
}