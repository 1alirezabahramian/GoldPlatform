<?php

namespace App\Domain\Financial\ValueObjects;

use App\Domain\Financial\Enums\FinancialAssetType;
use InvalidArgumentException;

final readonly class FinancialAssetId
{
    private string $externalId;

    public function __construct(
        private FinancialAssetType $type,
        string $externalId,
    ) {
        $externalId = trim($externalId);

        if ($externalId === '') {
            throw new InvalidArgumentException('Financial asset identifier cannot be empty.');
        }

        $this->externalId = $externalId;
    }

    public function type(): FinancialAssetType
    {
        return $this->type;
    }

    public function externalId(): string
    {
        return $this->externalId;
    }

    public function equals(self $other): bool
    {
        return $this->type === $other->type
            && $this->externalId === $other->externalId;
    }

    public function __toString(): string
    {
        return $this->type->value.':'.$this->externalId;
    }
}
