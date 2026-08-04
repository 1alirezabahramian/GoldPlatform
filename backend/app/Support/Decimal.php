<?php

namespace App\Support;

use InvalidArgumentException;

final class Decimal
{
    public static function normalize(string $value, int $scale = 8): string
    {
        return self::normalizeWithoutCompare($value, $scale);
    }

    public static function add(string $left, string $right, int $scale = 8): string
    {
        [$ln, $ld] = self::integerDigits($left, $scale);
        [$rn, $rd] = self::integerDigits($right, $scale);
        if ($ln === $rn) {
            return self::fromIntegerDigits(self::addDigits($ld, $rd), $ln, $scale);
        }
        $cmp = self::compareDigits($ld, $rd);
        if ($cmp === 0) { return self::normalizeWithoutCompare('0', $scale); }
        return $cmp > 0
            ? self::fromIntegerDigits(self::subtractDigits($ld, $rd), $ln, $scale)
            : self::fromIntegerDigits(self::subtractDigits($rd, $ld), $rn, $scale);
    }

    public static function subtract(string $left, string $right, int $scale = 8): string
    {
        $right = str_starts_with(trim($right), '-') ? ltrim(trim($right), '-') : '-'.trim($right);
        return self::add($left, $right, $scale);
    }

    public static function multiply(string $left, string $right, int $scale = 8): string
    {
        [$ln, $ld] = self::integerDigits($left, $scale);
        [$rn, $rd] = self::integerDigits($right, $scale);
        $product = self::multiplyDigits($ld, $rd);
        $product = strlen($product) > $scale ? substr($product, 0, -$scale) : '0';
        return self::fromIntegerDigits($product, $ln xor $rn, $scale);
    }

    public static function compare(string $left, string $right, int $scale = 8): int
    {
        [$ln, $ld] = self::integerDigits($left, $scale);
        [$rn, $rd] = self::integerDigits($right, $scale);
        if ($ln !== $rn) { return $ln ? -1 : 1; }
        $cmp = self::compareDigits($ld, $rd);
        return $ln ? -$cmp : $cmp;
    }

    private static function integerDigits(string $value, int $scale): array
    {
        $normalized = self::normalizeWithoutCompare($value, $scale);
        $negative = str_starts_with($normalized, '-');
        return [$negative, str_replace('.', '', ltrim($normalized, '-'))];
    }

    private static function normalizeWithoutCompare(string $value, int $scale): string
    {
        $value = trim($value);
        if (! preg_match('/^-?\d+(?:\.\d+)?$/', $value)) {
            throw new InvalidArgumentException('Invalid decimal value.');
        }
        $negative = str_starts_with($value, '-');
        [$whole, $fraction] = array_pad(explode('.', ltrim($value, '+-'), 2), 2, '');
        $whole = ltrim($whole, '0') ?: '0';
        $fraction = substr(str_pad($fraction, $scale, '0'), 0, $scale);
        $isZero = $whole === '0' && trim($fraction, '0') === '';
        return ($negative && ! $isZero ? '-' : '').$whole.($scale > 0 ? '.'.$fraction : '');
    }

    private static function addDigits(string $a, string $b): string
    {
        $a = strrev($a); $b = strrev($b); $carry = 0; $result = '';
        for ($i = 0, $n = max(strlen($a), strlen($b)); $i < $n; $i++) {
            $sum = (int) ($a[$i] ?? 0) + (int) ($b[$i] ?? 0) + $carry;
            $result .= (string) ($sum % 10); $carry = intdiv($sum, 10);
        }
        if ($carry) { $result .= (string) $carry; }
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

    private static function multiplyDigits(string $a, string $b): string
    {
        $result = array_fill(0, strlen($a) + strlen($b), 0);
        for ($i = strlen($a) - 1; $i >= 0; $i--) {
            for ($j = strlen($b) - 1; $j >= 0; $j--) {
                $pos = $i + $j + 1;
                $sum = $result[$pos] + ((int) $a[$i] * (int) $b[$j]);
                $result[$pos] = $sum % 10;
                $result[$pos - 1] += intdiv($sum, 10);
            }
        }
        return ltrim(implode('', $result), '0') ?: '0';
    }

    private static function compareDigits(string $a, string $b): int
    {
        $a = ltrim($a, '0') ?: '0'; $b = ltrim($b, '0') ?: '0';
        return strlen($a) <=> strlen($b) ?: (strcmp($a, $b) <=> 0);
    }

    private static function fromIntegerDigits(string $digits, bool $negative, int $scale): string
    {
        $digits = str_pad(ltrim($digits, '0') ?: '0', $scale + 1, '0', STR_PAD_LEFT);
        $whole = $scale ? substr($digits, 0, -$scale) : $digits;
        $fraction = $scale ? substr($digits, -$scale) : '';
        return self::normalizeWithoutCompare(($negative && trim($digits, '0') !== '' ? '-' : '').$whole.($scale ? '.'.$fraction : ''), $scale);
    }
}
