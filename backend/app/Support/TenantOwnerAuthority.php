<?php

namespace App\Support;

use App\Models\Tenant;
use App\Models\User;

final class TenantOwnerAuthority
{
    public static function allows(User $user, Tenant $tenant): bool
    {
        if ($tenant->owner_user_id === null || $user->tenant_id === null) {
            return false;
        }

        return (int) $tenant->owner_user_id === (int) $user->id
            && (int) $user->tenant_id === (int) $tenant->id;
    }
}
