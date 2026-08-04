<?php

namespace App\Domain\Trading\Validation;

interface TradingValidationRule
{
    public function validate(TradingValidationContext $context): ?ValidationFailure;
}
