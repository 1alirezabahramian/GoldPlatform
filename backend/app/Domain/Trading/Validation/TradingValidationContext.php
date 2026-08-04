<?php

namespace App\Domain\Trading\Validation;

use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use InvalidArgumentException;

final readonly class TradingValidationContext
{
    /** @var list<FinancialScope> */
    private array $relatedScopes;

    /**
     * @param list<FinancialScope> $relatedScopes
     */
    public function __construct(
        private string $operation,
        private FinancialScope $scope,
        private TraceId $traceId,
        private CorrelationId $correlationId,
        private IdempotencyKey $idempotencyKey,
        array $relatedScopes = [],
    ) {
        if (trim($operation) === '') {
            throw new InvalidArgumentException('Trading validation operation is required.');
        }

        foreach ($relatedScopes as $relatedScope) {
            if (! $relatedScope instanceof FinancialScope) {
                throw new InvalidArgumentException('Related scopes must be FinancialScope instances.');
            }
        }

        $this->relatedScopes = array_values($relatedScopes);
    }

    public function operation(): string
    {
        return $this->operation;
    }

    public function scope(): FinancialScope
    {
        return $this->scope;
    }

    public function traceId(): TraceId
    {
        return $this->traceId;
    }

    public function correlationId(): CorrelationId
    {
        return $this->correlationId;
    }

    public function idempotencyKey(): IdempotencyKey
    {
        return $this->idempotencyKey;
    }

    /** @return list<FinancialScope> */
    public function relatedScopes(): array
    {
        return $this->relatedScopes;
    }
}
