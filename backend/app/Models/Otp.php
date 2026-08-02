<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [

        'mobile',

        'otp',

        'purpose',

        'attempts',

        'verified',

        'expires_at',

        'verified_at',

        'ip_address',

        'user_agent',

    ];

    protected $casts = [

        'expires_at' => 'datetime',

        'verified_at' => 'datetime',

        'verified' => 'boolean',

    ];

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }
}
