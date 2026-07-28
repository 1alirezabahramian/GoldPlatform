<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalAccount extends Model
{
    use SoftDeletes;

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