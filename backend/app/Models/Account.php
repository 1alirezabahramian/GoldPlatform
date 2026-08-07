<?php

namespace App\Models;

use App\Exceptions\BusinessException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    /**
     * @throws BusinessException
     */
    protected static function booted(): void
    {
        static::updating(function (self $account): void {
            if ($account->isDirty('kimia_id')) {
                throw new BusinessException(
                    'A Kimia AccountId cannot be changed after synchronization.'
                );
            }
        });
    }

    protected $fillable = [

        'tenant_id',
        'kimia_id',
        'account_code',

        'group_id',
        'account_type',

        'name',
        'shop_name',

        'national_code',

        'mobile',
        'tel',

        'economic_code',

        'address',

        'postal_code',

        'birthday',

        'comment',

        'is_visible',

        'synced_at',
    ];

    protected $casts = [

        'birthday'   => 'datetime',
        'synced_at'  => 'datetime',
        'deleted_at' => 'datetime',

        'is_visible' => 'boolean',

    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * گروه حساب در کیمیا
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(AccountGroup::class);
    }

    /**
     * کاربر متصل به این حساب
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
