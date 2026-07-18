<?php

namespace App\Services\Kimia;

use Illuminate\Support\Facades\Http;

abstract class BaseKimiaService
{
    protected function client()
    {
        return Http::acceptJson()
            ->timeout(config('services.kimia.timeout'))
            ->withBasicAuth(
                config('services.kimia.username'),
                config('services.kimia.password')
            )
            ->baseUrl(config('services.kimia.base_url'));
    }
}