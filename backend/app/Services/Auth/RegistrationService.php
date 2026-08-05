<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $name = trim(implode(' ', array_filter([
                $data['first_name'] ?? null,
                $data['last_name'] ?? null,
            ])));

            return User::create([
                'mobile' => $data['mobile'],
                'name' => $name !== '' ? $name : null,
                'national_code' => $data['national_code'] ?? null,
                'password' => $data['password'],
                'mobile_verified' => true,
                'is_active' => true,
            ]);
        });
    }
}
