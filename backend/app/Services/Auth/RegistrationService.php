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

            $wallet = $user->wallet()->create([]);

            $wallet->accounts()->createMany([
                [
                    'code' => 'RIAL',
                    'title' => 'ریال',
                    'balance' => '0',
                    'blocked_balance' => '0',
                    'is_active' => true,
                ],
                [
                    'code' => 'GOLD18',
                    'title' => 'طلای ۱۸ عیار',
                    'balance' => '0',
                    'blocked_balance' => '0',
                    'is_active' => true,
                ],
            ]);

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
