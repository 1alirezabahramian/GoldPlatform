<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */

            $user = User::create([

                'mobile' => $data['mobile'],

                'first_name' => $data['first_name'] ?? null,

                'last_name' => $data['last_name'] ?? null,

                'national_code' => $data['national_code'] ?? null,

                // Laravel خودش Hash می‌کند
                'password' => $data['password'],

                'mobile_verified' => true,

                'is_active' => true,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Wallet
            |--------------------------------------------------------------------------
            */

            $user->wallet()->create([

                'rial_balance' => 0,

                'gold_balance' => 0.000,

                'blocked_rial' => 0,

                'blocked_gold' => 0.000,

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