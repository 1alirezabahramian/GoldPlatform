<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

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

    protected static function booted(): void
    {
        static::creating(function (FinancialTransaction $transaction): void {
            if (blank($transaction->uuid)) {
                $transaction->uuid = (string) Str::uuid();
            }
        });
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(
            LedgerEntry::class,
            'financial_transaction_id'
        );
    }
}
