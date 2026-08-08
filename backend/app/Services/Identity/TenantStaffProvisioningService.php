<?php

namespace App\Services\Identity;

use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

final class TenantStaffProvisioningService
{
    /**
     * @param  array{name:?string,mobile:string,username:string,role:string}  $attributes
     * @return array{user:User,temporary_password:string}
     */
    public function provision(
        Tenant $tenant,
        User $actor,
        array $attributes,
        ?string $requestId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): array {
        return DB::transaction(function () use ($tenant, $actor, $attributes, $requestId, $ipAddress, $userAgent): array {
            $role = Role::query()
                ->where('name', $attributes['role'])
                ->where('guard_name', 'web')
                ->firstOrFail();

            $temporaryPassword = Str::random(48);

            // Customer creation owns wallet/default-account provisioning through UserObserver.
            // Staff identities are intentionally created without that customer-only side effect.
            $user = User::withoutEvents(fn (): User => User::query()->create([
                'tenant_id' => $tenant->id,
                'name' => $attributes['name'] ?? null,
                'mobile' => $attributes['mobile'],
                'username' => $attributes['username'],
                'password' => $temporaryPassword,
                'must_change_password' => true,
                'password_changed_at' => null,
                'mobile_verified' => false,
                'is_active' => true,
            ]));

            $user->assignRole($role);

            AuditLog::query()->create([
                'actor_id' => $actor->id,
                'action' => 'tenant.staff.provisioned',
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'request_id' => $requestId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'before' => null,
                'after' => [
                    'tenant_id' => $tenant->id,
                    'username' => $user->username,
                    'role' => $role->name,
                    'must_change_password' => true,
                ],
                'metadata' => [
                    'authority' => 'tenant_owner',
                    'customer_wallet_provisioning' => 'not_applicable',
                ],
                'created_at' => now(),
            ]);

            return [
                'user' => $user,
                'temporary_password' => $temporaryPassword,
            ];
        });
    }
}
