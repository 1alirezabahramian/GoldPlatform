<?php

namespace App\Domain\Trading\ValueObjects;

use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class QuoteId
{
    private function __construct(private string $value)
    {
        if (! Str::isUuid($value)) {
            throw new InvalidArgumentException('Quote identifier must be a valid UUID.');
        }
    }

    public static function generate(): self
    {
        return new self((string) Str::uuid());
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return hash_equals($this->value, $other->value);
    }
}
