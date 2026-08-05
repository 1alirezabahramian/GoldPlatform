<?php

namespace App\Models;

use App\Enums\AssetType;
use App\Services\Wallet\BalanceProjectionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WalletAccount extends Model
{
    protected $fillable = [
        'wallet_id',
        'code',
        'title',
        'asset_type',
        'external_asset_id',
        'unit',
        'balance',
        'blocked_balance',
        'is_active',
    ];

    /**
     * Internal projection fields are never part of automatic API/Admin serialization.
     * Kimia remains the final customer balance authority for Money, Gold, Coin and Currency.
     */
    protected $hidden = [
        'balance',
        'blocked_balance',
        'available_balance',
    ];

    protected $casts = [
        'asset_type' => AssetType::class,
        'external_asset_id' => 'integer',
        'balance' => 'decimal:8',
        'blocked_balance' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function balanceReservations(): HasMany
    {
        return $this->hasMany(BalanceReservation::class);
    }

    public function getAvailableBalanceAttribute(): string
    {
        return app(BalanceProjectionService::class)->snapshot($this)['available'];
    }
}
