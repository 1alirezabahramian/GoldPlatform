<?php

namespace App\Domain\Trading\Validation;

final readonly class ValidationResult
{
    /** @param list<ValidationFailure> $failures */
    private function __construct(private array $failures)
    {
    }

    public static function valid(): self
    {
        return new self([]);
    }

    /** @param list<ValidationFailure> $failures */
    public static function invalid(array $failures): self
    {
        return new self(array_values($failures));
    }

    public function isValid(): bool
    {
        return $this->failures === [];
    }

    /** @return list<ValidationFailure> */
    public function failures(): array
    {
        return $this->failures;
    }
}
