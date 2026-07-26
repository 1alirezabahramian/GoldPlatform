<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerEntry extends Model
{
    protected $fillable = [
        'financial_transaction_id',
        'wallet_account_id',
        'entry_type',
        'amount',
        'currency',
        'description',
    ];

    public function financialTransaction(): BelongsTo
    {
        return $this->belongsTo(
            FinancialTransaction::class,
            'financial_transaction_id'
        );
    }

    public function walletAccount(): BelongsTo
    {
        return $this->belongsTo(
            WalletAccount::class,
            'wallet_account_id'
        );
    }
}