<?php

namespace App\Models;

use App\Exceptions\BusinessException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalAccount extends Model
{
    use SoftDeletes;

    /**
     * The provider and external identifier form the immutable external identity.
     *
     * @throws BusinessException
     */
    protected static function booted(): void
    {
        static::updating(function (self $account): void {
            if (
                $account->isDirty('provider')
                || $account->isDirty('external_id')
            ) {
                throw new BusinessException(
                    'An external account identity cannot be changed after synchronization.'
                );
            }
        });
    }

    protected $fillable = [
        'provider',
        'external_id',
        'code',
        'name',
        'type',
        'mobile',
        'national_id',
        'is_active',
        'sync_status',
        'sync_error',
        'sync_hash',
        'raw_data',
        'last_synced_at',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];
}
