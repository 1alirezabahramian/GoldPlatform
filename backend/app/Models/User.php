<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [

        'first_name',

        'last_name',

        'mobile',

        'name',

        'national_code',

        'group_id',

        'account_id',

        'mobile_verified',

        'is_active',

        'last_login_at',

        'email',

        'password',

    ];

    protected $hidden = [

        'password',

        'remember_token',

    ];

    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',

            'mobile_verified' => 'boolean',

            'is_active' => 'boolean',

            'last_login_at' => 'datetime',

            'password' => 'hashed',

        ];
    }

    /**
     * User Group
     */
    public function group()
    {
        return $this->belongsTo(UserGroup::class);
    }

    /**
     * Kimia Account
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Wallet
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}