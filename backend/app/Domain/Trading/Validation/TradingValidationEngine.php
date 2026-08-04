<?php

namespace App\Domain\Trading\Validation;

use InvalidArgumentException;

final readonly class TradingValidationEngine
{
    /** @var list<TradingValidationRule> */
    private array $rules;

    /** @param list<TradingValidationRule> $rules */
    public function __construct(array $rules)
    {
        foreach ($rules as $rule) {
            if (! $rule instanceof TradingValidationRule) {
                throw new InvalidArgumentException('Trading validation rules must implement TradingValidationRule.');
            }
        }

        $this->rules = array_values($rules);
    }

    public function validate(TradingValidationContext $context): ValidationResult
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
