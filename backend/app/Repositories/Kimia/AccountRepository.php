<?php

namespace App\Repositories\Kimia;

use App\Services\KimiaService;
use Illuminate\Http\Client\Response;

class AccountRepository
{
    public function __construct(
        protected KimiaService $kimia
    ) {
    }

    /**
     * لیست همه حساب‌ها
     */
    public function all(?int $accountType = null): array
    {
        $response = $this->kimia
            ->client()
            ->get('/api/account', [
                'accountType' => $accountType,
            ]);

        if (! $response->successful()) {
            return [];
        }

        return $response->json() ?? [];
    }

    /**
     * دریافت یک حساب
     */
    public function find(int $id): array|null
    {
        $response = $this->kimia
            ->client()
            ->get("/api/account/{$id}");

        if (! $response->successful()) {
            return null;
        }

        return $response->json();
    }

    /**
     * گروه‌های حساب
     */
    public function groups(?int $accountType = null): array
    {
        $response = $this->kimia
            ->client()
            ->get('/api/account/groups', [
                'accountType' => $accountType,
            ]);

        if (! $response->successful()) {
            return [];
        }

        return $response->json() ?? [];
    }

    /**
     * ایجاد حساب
     */
    public function create(array $data): Response
    {
        return $this->kimia
            ->client()
            ->post('/api/account', $data);
    }

    /**
     * بروزرسانی حساب
     */
    public function update(array $data): Response
    {
        return $this->kimia
            ->client()
            ->put('/api/account', $data);
    }
}