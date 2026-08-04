<?php

namespace App\Domain\Financial\ValueObjects;

use App\Domain\Financial\Enums\MoneyUnit;
use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        private ExactDecimal $amount,
        private MoneyUnit $unit,
    ) {
    }

    public static function fromString(string $amount, MoneyUnit $unit): self
    {
        return new self(ExactDecimal::fromString($amount), $unit);
    }

    public function amount(): ExactDecimal
    {
        return $this->amount;
    }

    public function unit(): MoneyUnit
    {
        return $this->unit;
    }

    public function add(self $other): self
    {
        $this->assertSameUnit($other);

        return new self($this->amount->add($other->amount), $this->unit);
    }

    public function subtract(self $other): self
    {
        $this->assertSameUnit($other);

        return new self($this->amount->subtract($other->amount), $this->unit);
    }

    public function equals(self $other): bool
    {
        return $this->unit === $other->unit
            && $this->amount->equals($other->amount);
    }

    private function assertSameUnit(self $other): void
    {
        if ($this->unit !== $other->unit) {
            throw new InvalidArgumentException('Money operations require the same unit.');
        }
    }
}
