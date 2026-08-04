<?php

namespace App\Domain\Trading\Validation;

interface OrderValidationRule
{
    public function validate(OrderValidationContext $context): ?ValidationFailure;
}
