<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountGroup extends Model
{
    protected $fillable = [

        'kimia_id',

        'account_type',

        'name',

        'is_active',

        'synced_at',

    ];

    protected $casts = [

        'is_active' => 'boolean',

        'synced_at' => 'datetime',

    ];
}