<?php

namespace App\Support;

final class ProductionConfigurationGuard
{
    /**
     * @return list<string>
     */
    public function violations(): array
    {
        $violations = [];

        if ((string) config('app.env') !== 'production') {
            $violations[] = 'APP_ENV must be production.';
        }

        if ((bool) config('app.debug')) {
            $violations[] = 'APP_DEBUG must be false.';
        }

        if (! str_starts_with((string) config('app.url'), 'https://')) {
            $violations[] = 'APP_URL must use HTTPS.';
        }

        if ((string) config('database.default') === 'sqlite') {
            $violations[] = 'Production database must not use SQLite.';
        }

        if (! (bool) config('session.secure')) {
            $violations[] = 'SESSION_SECURE_COOKIE must be true.';
        }

        if (! (bool) config('services.kimia.read_only', true)) {
            $violations[] = 'KIMIA_READ_ONLY must remain true.';
        }

        if ((bool) config('kimia_write.enabled', false)) {
            $violations[] = 'KIMIA_WRITE_ENABLED must remain false.';
        }

        return $violations;
    }
}
