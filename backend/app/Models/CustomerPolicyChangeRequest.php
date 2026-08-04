<?php

namespace App\Models;

use App\Enums\CustomerPolicyChangeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CustomerPolicyChangeRequest extends Model
{
    protected $fillable = [
        'uuid',
        'customer_trading_policy_id',
        'proposed_changes',
        'status',
        'reason',
        'review_note',
        'created_by',
        'submitted_by',
        'reviewed_by',
        'submitted_at',
        'reviewed_at',
        'applied_at',
    ];

    protected function casts(): array
    {
        return [
            'proposed_changes' => 'array',
            'status' => CustomerPolicyChangeStatus::class,
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'applied_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $request): void {
            $request->uuid ??= (string) Str::uuid();
            $request->status ??= CustomerPolicyChangeStatus::Draft;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(CustomerTradingPolicy::class, 'customer_trading_policy_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
