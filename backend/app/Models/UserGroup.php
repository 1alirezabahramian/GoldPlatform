<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserGroup extends Model
{
    protected $fillable = [

        'title',

        'buy_commission',

        'sell_commission',

        'discount_percent',

        'priority',

        'is_active',

        'description',

    ];

    protected $casts = [

        'buy_commission'   => 'decimal:2',

        'sell_commission'  => 'decimal:2',

        'discount_percent' => 'decimal:2',

        'priority'         => 'integer',

        'is_active'        => 'boolean',

    ];

    /**
     * Users
     */
    public function users(): HasMany
    {
        return $this->hasMany(
            User::class,
            'group_id'
        );
    }
}