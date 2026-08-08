<?php

namespace App\Services\Kimia;

use App\Models\User;
use App\Repositories\Kimia\Read\BalanceReadRepository;
use App\Tenancy\TenantContext;

final class AuthenticatedCustomerKimiaBalanceReadService
{
    public function __construct(
        private readonly AuthenticatedCustomerKimiaAccountResolver $resolver,
        private readonly BalanceReadRepository $balances,
    ) {}

    /**
     * Read Kimia balances only after the complete Tenant -> User -> Account -> Kimia chain is proven.
     *
     * @return array{resolved: bool, reason: string, kimia_account_id: ?string, balances: ?array}
     */
    public function read(User $user, TenantContext $context): array
    {
        $binding = $this->resolver->resolve($user, $context);

        if (! $binding['resolved']) {
            return [
                'resolved' => false,
                'reason' => $binding['reason'],
                'kimia_account_id' => null,
                'balances' => null,
            ];
        }

        $kimiaAccountId = (int) $binding['kimia_account_id'];

        return [
            'resolved' => true,
            'reason' => 'RESOLVED',
            'kimia_account_id' => (string) $kimiaAccountId,
            'balances' => $this->balances->forAccount($kimiaAccountId),
        ];
    }
}
