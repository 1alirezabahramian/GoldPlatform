<?php

namespace App\Domain\Trading\Validation\Rules;

use App\Domain\Trading\Validation\TradingValidationContext;
use App\Domain\Trading\Validation\TradingValidationRule;
use App\Domain\Trading\Validation\ValidationFailure;

final readonly class MatchingFinancialScopeRule implements TradingValidationRule
{
    public function validate(TradingValidationContext $context): ?ValidationFailure
    {
        $expected = $context->scope()->key();

        foreach ($context->relatedScopes() as $relatedScope) {
            if (! hash_equals($expected, $relatedScope->key())) {
                return new ValidationFailure(
                    code: 'trading.scope_mismatch',
                    message: 'Trading entities must belong to the same financial scope.',
                );
            }
        }

        return null;
    }
}
