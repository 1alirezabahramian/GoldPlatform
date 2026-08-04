<?php

namespace App\Domain\Trading\Validation;

use InvalidArgumentException;

final readonly class OrderValidationEngine
{
    /** @var list<OrderValidationRule> */
    private array $rules;

    /** @param list<OrderValidationRule> $rules */
    public function __construct(array $rules)
    {
        foreach ($rules as $rule) {
            if (! $rule instanceof OrderValidationRule) {
                throw new InvalidArgumentException('Order validation rules must implement OrderValidationRule.');
            }
        }

        $this->rules = array_values($rules);
    }

    public function validate(OrderValidationContext $context): ValidationResult
    {
        $failures = [];

        foreach ($this->rules as $rule) {
            $failure = $rule->validate($context);
            if ($failure !== null) {
                $failures[] = $failure;
            }
        }

        return $failures === []
            ? ValidationResult::valid()
            : ValidationResult::invalid($failures);
    }
}
