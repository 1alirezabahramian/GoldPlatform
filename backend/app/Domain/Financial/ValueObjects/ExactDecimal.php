<?php

namespace App\Domain\Financial\ValueObjects;

use InvalidArgumentException;

final readonly class ExactDecimal
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = self::normalize($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function scale(): int
    {
        $position = strpos($this->value, '.');

        return $position === false
            ? 0
            : strlen($this->value) - $position - 1;
    }

    public function add(self $other): self
    {
        $scale = max($this->scale(), $other->scale());

        return new self(bcadd($this->value, $other->value, $scale));
    }

    public function subtract(self $other): self
    {
        $scale = max($this->scale(), $other->scale());

        return new self(bcsub($this->value, $other->value, $scale));
    }

    public function compare(self $other): int
    {
        return bccomp(
            $this->value,
            $other->value,
            max($this->scale(), $other->scale())
        );
    }

    public function equals(self $other): bool
    {
        return $this->compare($other) === 0;
    }

    public function isZero(): bool
    {
        return $this->compare(new self('0')) === 0;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function normalize(string $value): string
    {
        $value = trim($value);

        if ($value === '' || ! preg_match('/^-?(?:0|[1-9]\d*)(?:\.\d+)?$/', $value)) {
            throw new InvalidArgumentException('ExactDecimal requires a plain decimal string.');
        }

        $negative = str_starts_with($value, '-');
        $unsigned = $negative ? substr($value, 1) : $value;
        [$integer, $fraction] = array_pad(explode('.', $unsigned, 2), 2, null);

        $integer = ltrim($integer, '0');
        $integer = $integer === '' ? '0' : $integer;

        if ($fraction !== null) {
            $fraction = rtrim($fraction, '0');
        }

        $normalized = $fraction === null || $fraction === ''
            ? $integer
            : $integer.'.'.$fraction;

        if ($normalized === '0') {
            return '0';
        }

        return $negative ? '-'.$normalized : $normalized;
    }
}
