<?php

namespace Tests\Unit\Financial;

use App\Domain\Financial\Enums\FinancialAssetType;
use App\Domain\Financial\Enums\MoneyUnit;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FinancialAssetBoundaryTest extends TestCase
{
    #[Test]
    public function it_contains_only_confirmed_financial_asset_types(): void
    {
        $this->assertSame(
            ['money', 'gold', 'coin', 'currency'],
            array_map(
                static fn (FinancialAssetType $type): string => $type->value,
                FinancialAssetType::cases(),
            ),
        );

        $this->assertNotContains('custody', array_column(FinancialAssetType::cases(), 'value'));
    }

    #[Test]
    public function it_keeps_rial_and_toman_as_explicit_distinct_units(): void
    {
        $this->assertSame('rial', MoneyUnit::RIAL->value);
        $this->assertSame('toman', MoneyUnit::TOMAN->value);
        $this->assertNotSame(MoneyUnit::RIAL, MoneyUnit::TOMAN);
    }
}
