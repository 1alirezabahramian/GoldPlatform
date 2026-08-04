<?php

namespace App\Domain\Financial\ValueObjects;

use InvalidArgumentException;

final readonly class FinancialScope
{
    public function __construct(
        private string $tenantId,
        private ?string $companyId = null,
        private ?string $branchId = null,
    ) {
        if (trim($tenantId) === '') {
            throw new InvalidArgumentException('Financial scope requires a tenant identifier.');
        }

        if ($branchId !== null && $companyId === null) {
            throw new InvalidArgumentException('A branch-scoped financial operation requires a company identifier.');
        }
    }

    public function tenantId(): string
    {
        return $this->tenantId;
    }

    public function companyId(): ?string
    {
        return $this->companyId;
    }

    public function branchId(): ?string
    {
        return $this->branchId;
    }

    public function key(): string
    {
        return implode(':', [
            'tenant', $this->tenantId,
            'company', $this->companyId ?? '*',
            'branch', $this->branchId ?? '*',
        ]);
    }

    public function belongsToTenant(string $tenantId): bool
    {
        return hash_equals($this->tenantId, $tenantId);
    }
}
