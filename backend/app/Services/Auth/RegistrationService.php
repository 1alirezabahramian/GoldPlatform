<?php

namespace App\Services\Auth;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    public function register(array $data): User
    {
        return $this->registerWithTenant($data, null);
    }

    public function registerForTenant(array $data, Tenant $tenant): User
    {
        return $this->registerWithTenant($data, $tenant->id);
    }

    private function registerWithTenant(array $data, ?int $tenantId): User
    {
        return DB::transaction(function () use ($data, $tenantId) {
            $name = trim(implode(' ', array_filter([
                $data['first_name'] ?? null,
                $data['last_name'] ?? null,
            ])));

            $user = User::create([
                'mobile' => $data['mobile'],
                'name' => $name !== '' ? $name : null,
                'national_code' => $data['national_code'] ?? null,
                'password' => $data['password'],
                'mobile_verified' => true,
                'is_active' => true,
                'tenant_id' => $tenantId,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Wallet lifecycle
            |--------------------------------------------------------------------------
            |
            | UserObserver is the canonical owner of creating the user's wallet and
            | default internal wallet-account projections after User creation.
            | RegistrationService must not duplicate that observer side effect.
            |
            */

            /*
            |--------------------------------------------------------------------------
            | TODO
            |--------------------------------------------------------------------------
            |
            | Create Kimia Account
            | Link Account
            | Assign Default Group
            |
            */

            return $user;
        });
    }
}
