<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = [

        'mobile',

        'code',

        'purpose',

        'attempts',

        'resend_count',

        'expires_at',

        'verified_at',

        'ip_address',

        'user_agent',

    ];

    protected $casts = [

        'expires_at' => 'datetime',

        'verified_at' => 'datetime',

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
        return $this->verified_at !== null;
    }
}