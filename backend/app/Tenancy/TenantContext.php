<?php

namespace App\Tenancy;

use App\Models\Tenant;
use LogicException;

class TenantContext
{
    private ?Tenant $tenant = null;

    public function activate(Tenant $tenant): void
    {
        if ($this->tenant !== null && ! $this->tenant->is($tenant)) {
            throw new LogicException(
                'Tenant context cannot switch inside the same execution scope.'
            );
        }

        $this->tenant = $tenant;
    }

    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    public function tenant(): Tenant
    {
        if ($this->tenant === null) {
            throw new LogicException('Tenant context has not been resolved.');
        }

        return $this->tenant;
    }

    public function tenantOrNull(): ?Tenant
    {
        return $this->tenant;
    }
}
