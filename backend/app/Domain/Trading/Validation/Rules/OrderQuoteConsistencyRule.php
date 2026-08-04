<?php

namespace App\Domain\Trading\Validation\Rules;

use App\Domain\Trading\Validation\OrderValidationContext;
use App\Domain\Trading\Validation\OrderValidationRule;
use App\Domain\Trading\Validation\ValidationFailure;

final class OrderQuoteConsistencyRule implements OrderValidationRule
{
    public function validate(OrderValidationContext $context): ?ValidationFailure
    {
        $order = $context->order();
        $quote = $context->quote();
        $scopeKey = $context->scope()->key();

        if ($order->scope()->key() !== $scopeKey || $quote->scope()->key() !== $scopeKey) {
            return new ValidationFailure(
                'trading.order.scope_mismatch',
                'Order, quote and command scope must match exactly.',
            );
        }

        if (! $order->quoteId()->equals($quote->id())) {
            return new ValidationFailure(
                'trading.order.quote_mismatch',
                'Order does not reference the supplied quote.',
            );
        }

        if (! $order->correlationId()->equals($quote->correlationId())) {
            return new ValidationFailure(
                'trading.order.correlation_mismatch',
                'Order and quote correlation identifiers must match.',
            );
        }

        return null;
    }
}
