<?php

namespace App\Console\Commands;

use App\Services\Tenancy\UserTenancyBindingPreflightService;
use Illuminate\Console\Command;

class InspectUserTenancyBindingReadiness extends Command
{
    protected $signature = 'tenancy:inspect-user-binding-readiness {--json}';

    protected $description = 'Inspect user tenancy/account-binding readiness without mutating data';

    public function handle(UserTenancyBindingPreflightService $preflight): int
    {
        $result = $preflight->inspect();

        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        foreach ($result as $key => $value) {
            $this->line(sprintf('%s: %s', $key, is_bool($value) ? ($value ? 'true' : 'false') : (string) $value));
        }

        if (! $result['tenant_id_column_exists']) {
            $this->warn('users.tenant_id is not present; no tenant assignment is inferred.');
        }

        if ($result['duplicate_account_bindings'] > 0) {
            $this->warn('Duplicate users.account_id bindings exist; uniqueness migration must remain blocked.');
        }

        return self::SUCCESS;
    }
}
