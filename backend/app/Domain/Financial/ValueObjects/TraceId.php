<?php

namespace App\Domain\Financial\ValueObjects;

use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class TraceId
{
    private function __construct(private string $value)
    {
        if (! Str::isUuid($value)) {
            throw new InvalidArgumentException('TraceId must be a valid UUID.');
        }
    }

    public static function generate(): self
    {
        return new self((string) Str::uuid());
    }

    public static function fromString(string $value): self
    {
        return new self(strtolower(trim($value)));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
