<?php

namespace App\Services\Kimia;

use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\Schema;

final class AuthenticatedCustomerKimiaAccountResolver
{
    /**
     * Resolve only when the complete Tenant -> User -> Account -> Kimia identity chain is proven.
     *
     * @return array{resolved: bool, reason: string, kimia_account_id: ?string}
     */
    public function resolve(User $user, TenantContext $context): array
    {
        $tenant = $context->tenantOrNull();

        if ($tenant === null) {
            return $this->blocked('TENANT_CONTEXT_REQUIRED');
        }

        if ($user->tenant_id === null || (int) $user->tenant_id !== (int) $tenant->id) {
            return $this->blocked('USER_TENANT_MISMATCH');
        }

        if ($user->account_id === null) {
            return $this->blocked('CUSTOMER_ACCOUNT_BINDING_REQUIRED');
        }

        if (! Schema::hasColumn('accounts', 'tenant_id')) {
            return $this->blocked('ACCOUNT_TENANT_OWNERSHIP_NOT_PROVEN');
        }

        $account = $user->account;

        if ($account === null) {
            return $this->blocked('CUSTOMER_ACCOUNT_NOT_FOUND');
        }

        if ($account->tenant_id === null) {
            return $this->blocked('ACCOUNT_TENANT_OWNERSHIP_REQUIRED');
        }

        if ((int) $account->tenant_id !== (int) $tenant->id) {
            return $this->blocked('ACCOUNT_TENANT_MISMATCH');
        }

        $kimiaId = trim((string) $account->kimia_id);
        if ($kimiaId === '') {
            return $this->blocked('KIMIA_ACCOUNT_ID_REQUIRED');
        }

        return [
            'resolved' => true,
            'reason' => 'RESOLVED',
            'kimia_account_id' => $kimiaId,
        ];
    }

    /** @return array{resolved: false, reason: string, kimia_account_id: null} */
    private function blocked(string $reason): array
    {
        return [
            'resolved' => false,
            'reason' => $reason,
            'kimia_account_id' => null,
        ];
    }
}
