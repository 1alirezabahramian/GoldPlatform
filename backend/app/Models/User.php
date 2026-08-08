<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'mobile', 'username', 'name', 'national_code', 'group_id',
        'tenant_id', 'account_id', 'referrer_user_id', 'referral_code',
        'mobile_verified', 'is_active', 'last_login_at', 'email', 'password',
        'must_change_password', 'password_changed_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mobile_verified' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'must_change_password' => 'boolean',
            'password_changed_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function group(): BelongsTo { return $this->belongsTo(UserGroup::class, 'group_id'); }
    public function account(): BelongsTo { return $this->belongsTo(Account::class); }
    public function referrer(): BelongsTo { return $this->belongsTo(self::class, 'referrer_user_id'); }
    public function referrals(): HasMany { return $this->hasMany(self::class, 'referrer_user_id'); }
    public function wallet(): HasOne { return $this->hasOne(Wallet::class); }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
}
