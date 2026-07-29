<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KimiaCoin extends Model
{
    protected $fillable = [
        'kimia_id',
        'name',
        'fineness',
        'weight',
        'type',
        'is_visible',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'fineness' => 'decimal:4',
            'weight' => 'decimal:4',
            'type' => 'integer',
            'is_visible' => 'boolean',
            'synced_at' => 'datetime',
        ];
    }
}
