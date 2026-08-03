<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'status',
        'gold_weight',
        'gold_price',
        'commission',
        'total_price',
        'description',
    ];

    protected $casts = [
        'gold_weight' => 'decimal:3',
        'gold_price' => 'decimal:0',
        'commission' => 'decimal:0',
        'total_price' => 'decimal:0',
    ];

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }
}
