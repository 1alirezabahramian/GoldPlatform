<?php

namespace App\Console\Commands;

use App\Support\ProductionConfigurationGuard;
use Illuminate\Console\Command;

class ValidateProductionConfiguration extends Command
{
    protected $signature = 'ops:validate-production-config';

    protected $description = 'Validate safety-critical production configuration.';

    public function handle(ProductionConfigurationGuard $guard): int
    {
        $violations = $guard->violations();

        if ($violations !== []) {
            foreach ($violations as $violation) {
                $this->error($violation);
            }

            return self::FAILURE;
        }

        $this->info('Production configuration validation passed.');

        return self::SUCCESS;
    }
}
