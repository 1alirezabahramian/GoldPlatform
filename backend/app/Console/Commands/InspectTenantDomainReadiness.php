<?php

namespace App\Console\Commands;

use App\Tenancy\TenantDomainReadinessService;
use Illuminate\Console\Command;

class InspectTenantDomainReadiness extends Command
{
    protected $signature = 'tenancy:inspect-domain-readiness {tenant} {host} {--json}';

    protected $description = 'Inspect tenant domain runtime readiness without mutating data.';

    public function handle(TenantDomainReadinessService $service): int
    {
        $result = $service->inspect(
            (string) $this->argument('tenant'),
            (string) $this->argument('host')
        );

        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } else {
            foreach ($result as $key => $value) {
                $this->line($key.': '.(is_bool($value) ? ($value ? 'true' : 'false') : (string) $value));
            }
        }

        return $result['runtime_activation_ready'] ? self::SUCCESS : self::FAILURE;
    }
}
