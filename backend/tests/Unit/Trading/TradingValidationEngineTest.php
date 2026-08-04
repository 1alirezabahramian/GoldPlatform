<?php

namespace Tests\Unit\Trading;

use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\Validation\Rules\MatchingFinancialScopeRule;
use App\Domain\Trading\Validation\TradingValidationContext;
use App\Domain\Trading\Validation\TradingValidationEngine;
use PHPUnit\Framework\TestCase;

final class TradingValidationEngineTest extends TestCase
{
    public function test_matching_scopes_are_valid(): void
    {
        $scope = new FinancialScope('tenant-a');
        $engine = new TradingValidationEngine([new MatchingFinancialScopeRule()]);

        $result = $engine->validate($this->context($scope, [$scope]));

        self::assertTrue($result->isValid());
        self::assertSame([], $result->failures());
    }

    public function test_cross_tenant_scope_is_rejected(): void
    {
        $engine = new TradingValidationEngine([new MatchingFinancialScopeRule()]);

        $result = $engine->validate($this->context(
            new FinancialScope('tenant-a'),
            [new FinancialScope('tenant-b')],
        ));

        self::assertFalse($result->isValid());
        self::assertCount(1, $result->failures());
        self::assertSame('trading.scope_mismatch', $result->failures()[0]->code());
    }

    /** @param list<FinancialScope> $relatedScopes */
    private function context(FinancialScope $scope, array $relatedScopes): TradingValidationContext
    {
        return new TradingValidationContext(
            operation: 'order.submit',
            scope: $scope,
            traceId: TraceId::generate(),
            correlationId: CorrelationId::generate(),
            idempotencyKey: IdempotencyKey::fromString('order-submit-1'),
            relatedScopes: $relatedScopes,
        );
    }
}
