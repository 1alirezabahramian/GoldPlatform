<?php

namespace App\Services\Kimia;

use InvalidArgumentException;

final class RialTomanConverter
{
    public function toToman(string|int $rial): string
    {
        $value = trim((string) $rial);

        if (! preg_match('/^-?\d+(?:\.\d+)?$/', $value)) {
            throw new InvalidArgumentException('Kimia rial value must be an exact decimal string.');
        }

        $fractionDigits = str_contains($value, '.')
            ? strlen(substr(strrchr($value, '.'), 1))
            : 0;

        $result = bcdiv($value, '10', $fractionDigits + 1);

        if (str_contains($result, '.')) {
            $result = rtrim(rtrim($result, '0'), '.');
        }

        return $result === '-0' ? '0' : $result;
    }
}
