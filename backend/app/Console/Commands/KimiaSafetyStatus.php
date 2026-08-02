<?php

namespace App\Console\Commands;

use App\Integrations\Kimia\Safety\KimiaWriteGate;
use Illuminate\Console\Command;

class KimiaSafetyStatus extends Command
{
    protected $signature = 'kimia:safety-status';

    protected $description = 'Verify that live Kimia writes are disabled';

    public function handle(KimiaWriteGate $writeGate): int
    {
        if ($writeGate->isEnabled()) {
            $this->error(
                'Kimia live writes are ENABLED. The stabilization checkpoint is blocked.'
            );

            return self::FAILURE;
        }

        $this->info('Kimia live writes are disabled (safe read-only mode).');

        return self::SUCCESS;
    }
}
