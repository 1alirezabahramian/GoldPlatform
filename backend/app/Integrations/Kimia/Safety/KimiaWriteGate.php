<?php

namespace App\Integrations\Kimia\Safety;

use App\Integrations\Kimia\Exceptions\KimiaWriteDisabledException;

class KimiaWriteGate
{
    public function isEnabled(): bool
    {
        return filter_var(
            config('services.kimia.writes_enabled', false),
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        ) === true;
    }

    /**
     * @throws KimiaWriteDisabledException
     */
    public function assertAllowed(string $method, string $uri): void
    {
        if ($this->isEnabled()) {
            return;
        }

        throw new KimiaWriteDisabledException(sprintf(
            'Kimia %s %s was blocked because live writes are disabled.',
            strtoupper($method),
            $uri
        ));
    }
}
