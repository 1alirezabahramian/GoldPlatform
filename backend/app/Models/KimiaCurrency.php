<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KimiaCurrency extends Model
{
    protected $fillable = [
        'kimia_id',
        'name',
        'is_visible',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'kimia_id' => 'integer',
            'is_visible' => 'boolean',
            'synced_at' => 'datetime',
        ];
    }
}
