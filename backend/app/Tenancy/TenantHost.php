<?php

namespace App\Tenancy;

final class TenantHost
{
    public static function normalize(string $host): string
    {
        $normalized = strtolower(trim($host));

        if ($normalized === '') {
            return '';
        }

        $withoutPort = preg_replace('/:\d+$/', '', $normalized);

        return rtrim($withoutPort ?? $normalized, '.');
    }
}
