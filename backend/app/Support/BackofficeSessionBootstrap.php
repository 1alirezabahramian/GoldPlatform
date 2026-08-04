<?php

namespace App\Support;

use App\Models\User;

final class BackofficeSessionBootstrap
{
    /**
     * @var array<string, list<array{code: string, path: string, permission: string}>>
     */
    private const NAVIGATION = [
        'admin' => [
            ['code' => 'audit_logs', 'path' => '/admin/audit-logs', 'permission' => 'audit-logs.view'],
            ['code' => 'outbox', 'path' => '/admin/outbox', 'permission' => 'outbox.view'],
            ['code' => 'customer_policies', 'path' => '/admin/customer-policies', 'permission' => 'customer-policies.view'],
        ],
        'operator' => [
            ['code' => 'order_queue', 'path' => '/operator/orders/queue', 'permission' => 'orders.queue.view'],
            ['code' => 'delivery_queue', 'path' => '/operator/deliveries/queue', 'permission' => 'deliveries.queue.view'],
        ],
    ];

    /** @return array<string, mixed> */
    public function for(User $user, string $panel): array
    {
        $roles = $user->getRoleNames()->sort()->values()->all();
        $permissions = $user->getAllPermissions()
            ->pluck('name')
            ->sort()
            ->values()
            ->all();

        $permissionLookup = array_fill_keys($permissions, true);

        $navigation = array_values(array_filter(
            self::NAVIGATION[$panel] ?? [],
            static fn (array $item): bool => isset($permissionLookup[$item['permission']]),
        ));

        return [
            'panel' => $panel,
            'session' => [
                'authenticated' => true,
                'user' => [
                    'display_name' => $this->displayName($user),
                    'mobile_masked' => $this->maskMobile($user->mobile),
                    'is_active' => (bool) $user->is_active,
                    'last_login_at' => $user->last_login_at?->toIso8601String(),
                ],
                'roles' => $roles,
                'permissions' => $permissions,
            ],
            'navigation' => $navigation,
            'capabilities' => $permissions,
        ];
    }

    private function displayName(User $user): string
    {
        $fullName = trim(implode(' ', array_filter([
            $user->first_name,
            $user->last_name,
        ])));

        return $fullName !== '' ? $fullName : (string) ($user->name ?? '');
    }

    private function maskMobile(?string $mobile): ?string
    {
        if ($mobile === null || mb_strlen($mobile) < 7) {
            return null;
        }

        return mb_substr($mobile, 0, 4).'***'.mb_substr($mobile, -4);
    }
}
