<?php

namespace App\Support;

final class BackofficePermissionCatalog
{
    public const ADMIN_ACCESS = 'admin.access';

    public const OPERATOR_ACCESS = 'operator.access';

    /** @return list<string> */
    public static function all(): array
    {
        return [
            self::ADMIN_ACCESS,
            self::OPERATOR_ACCESS,
        ];
    }

    /** @return list<string> */
    public static function adminDefaults(): array
    {
        return [self::ADMIN_ACCESS];
    }

    /** @return list<string> */
    public static function operatorDefaults(): array
    {
        return [self::OPERATOR_ACCESS];
    }
}
