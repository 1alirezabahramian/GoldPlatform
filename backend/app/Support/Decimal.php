<?php

namespace App\Support;

use InvalidArgumentException;

final class Decimal
{
    public static function normalize(string $value, int $scale = 8): string
    {
        $value = trim($value);
        if (! preg_match('/^-?\d+(?:\.\d+)?$/', $value)) {
            throw new InvalidArgumentException('Invalid decimal value.');
        }

        $negative = str_starts_with($value, '-');
        $unsigned = ltrim($value, '+-');
        [$whole, $fraction] = array_pad(explode('.', $unsigned, 2), 2, '');
        $whole = ltrim($whole, '0') ?: '0';
        $fraction = substr(str_pad($fraction, $scale, '0'), 0, $scale);

        $result = $whole.($scale > 0 ? '.'.$fraction : '');
        if ($negative && self::compare($result, '0', $scale) !== 0) {
            return '-'.$result;
        }

        return $result;
    }

    public static function add(string $left, string $right, int $scale = 8): string
    {
        [$leftNegative, $leftDigits] = self::integerDigits($left, $scale);
        [$rightNegative, $rightDigits] = self::integerDigits($right, $scale);

        if ($leftNegative === $rightNegative) {
            return self::fromIntegerDigits(self::addDigits($leftDigits, $rightDigits), $leftNegative, $scale);
        }

        $comparison = self::compareDigits($leftDigits, $rightDigits);
        if ($comparison === 0) {
            return self::normalize('0', $scale);
        }

        if ($comparison > 0) {
            return self::fromIntegerDigits(self::subtractDigits($leftDigits, $rightDigits), $leftNegative, $scale);
        }

        return self::fromIntegerDigits(self::subtractDigits($rightDigits, $leftDigits), $rightNegative, $scale);
    }

    public static function subtract(string $left, string $right, int $scale = 8): string
    {
        $right = str_starts_with(trim($right), '-') ? ltrim(trim($right), '-') : '-'.trim($right);
        return self::add($left, $right, $scale);
    }

    public static function compare(string $left, string $right, int $scale = 8): int
    {
        [$leftNegative, $leftDigits] = self::integerDigits($left, $scale);
        [$rightNegative, $rightDigits] = self::integerDigits($right, $scale);

        if ($leftNegative !== $rightNegative) {
            return $leftNegative ? -1 : 1;
        }

        $comparison = self::compareDigits($leftDigits, $rightDigits);
        return $leftNegative ? -$comparison : $comparison;
    }

    private static function integerDigits(string $value, int $scale): array
    {
        $normalized = self::normalizeWithoutCompare($value, $scale);
        $negative = str_starts_with($normalized, '-');
        $unsigned = ltrim($normalized, '-');
        return [$negative, str_replace('.', '', $unsigned)];
    }

    private static function normalizeWithoutCompare(string $value, int $scale): string
    {
        $value = trim($value);
        if (! preg_match('/^-?\d+(?:\.\d+)?$/', $value)) {
            throw new InvalidArgumentException('Invalid decimal value.');
        }
        $negative = str_starts_with($value, '-');
        $unsigned = ltrim($value, '+-');
        [$whole, $fraction] = array_pad(explode('.', $unsigned, 2), 2, '');
        $whole = ltrim($whole, '0') ?: '0';
        $fraction = substr(str_pad($fraction, $scale, '0'), 0, $scale);
        $isZero = ((int) $whole === 0) && trim($fraction, '0') === '';
        return ($negative && ! $isZero ? '-' : '').$whole.($scale > 0 ? '.'.$fraction : '');
    }

    private static function addDigits(string $a, string $b): string
    {
        $a = strrev($a); $b = strrev($b); $carry = 0; $result = '';
        $length = max(strlen($a), strlen($b));
        for ($i = 0; $i < $length; $i++) {
            $sum = (int) ($a[$i] ?? 0) + (int) ($b[$i] ?? 0) + $carry;
            $result .= (string) ($sum % 10); $carry = intdiv($sum, 10);
        }
        if ($carry > 0) { $result .= (string) $carry; }
        return strrev($result);
    }

    private static function subtractDigits(string $a, string $b): string
    {
        $a = strrev($a); $b = strrev($b); $borrow = 0; $result = '';
        for ($i = 0; $i < strlen($a); $i++) {
            $digit = (int) $a[$i] - $borrow - (int) ($b[$i] ?? 0);
            if ($digit < 0) { $digit += 10; $borrow = 1; } else { $borrow = 0; }
            $result .= (string) $digit;
        }
        return ltrim(strrev($result), '0') ?: '0';
    }

    private static function compareDigits(string $a, string $b): int
    {
        $a = ltrim($a, '0') ?: '0'; $b = ltrim($b, '0') ?: '0';
        return strlen($a) <=> strlen($b) ?: strcmp($a, $b) <=> 0;
    }

    private static function fromIntegerDigits(string $digits, bool $negative, int $scale): string
    {
        $digits = str_pad(ltrim($digits, '0') ?: '0', $scale + 1, '0', STR_PAD_LEFT);
        $whole = $scale > 0 ? substr($digits, 0, -$scale) : $digits;
        $fraction = $scale > 0 ? substr($digits, -$scale) : '';
        $value = ($negative && trim($digits, '0') !== '' ? '-' : '').$whole.($scale > 0 ? '.'.$fraction : '');
        return self::normalizeWithoutCompare($value, $scale);
    }
}
