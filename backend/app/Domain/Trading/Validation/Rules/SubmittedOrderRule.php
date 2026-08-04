<?php

namespace App\Domain\Trading\Validation\Rules;

use App\Domain\Trading\Enums\OrderStatus;
use App\Domain\Trading\Validation\OrderValidationContext;
use App\Domain\Trading\Validation\OrderValidationRule;
use App\Domain\Trading\Validation\ValidationFailure;

final class SubmittedOrderRule implements OrderValidationRule
{
    public function validate(OrderValidationContext $context): ?ValidationFailure
    {
        if ($context->order()->status() !== OrderStatus::SUBMITTED) {
            return new ValidationFailure(
                'trading.order.not_submitted',
                'Only a submitted order can enter an approval or rejection decision.',
            );
        }

        return null;
    }
}
