<?php

namespace App\Support;

use InvalidArgumentException;

final class IdentityOnboardingPolicy
{
    public const CUSTOMER_AUTH_OTP = 'otp';
    public const STAFF_AUTH_PASSWORD = 'password';

    public const REGISTRATION_MANUAL = 'manual';
    public const REGISTRATION_ASSISTED = 'assisted';
    public const REGISTRATION_AUTOMATIC = 'automatic';

    public static function registrationModes(): array
    {
        return [
            self::REGISTRATION_MANUAL,
            self::REGISTRATION_ASSISTED,
            self::REGISTRATION_AUTOMATIC,
        ];
    }

    public static function requiresKimiaCustomerCreate(string $mode): bool
    {
        self::assertRegistrationMode($mode);

        return in_array($mode, [
            self::REGISTRATION_ASSISTED,
            self::REGISTRATION_AUTOMATIC,
        ], true);
    }

    public static function canActivateRegistrationMode(
        string $mode,
        bool $jibitReady,
        bool $kimiaCustomerCreateReady
    ): bool {
        self::assertRegistrationMode($mode);

        if (! $jibitReady) {
            return false;
        }

        if (self::requiresKimiaCustomerCreate($mode) && ! $kimiaCustomerCreateReady) {
            return false;
        }

        return true;
    }

    private static function assertRegistrationMode(string $mode): void
    {
        if (! in_array($mode, self::registrationModes(), true)) {
            throw new InvalidArgumentException("Unsupported customer registration mode: {$mode}");
        }
    }
}
