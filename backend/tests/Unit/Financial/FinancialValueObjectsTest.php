<?php

namespace Tests\Unit\Financial;

use App\Domain\Financial\Enums\FinancialAssetType;
use App\Domain\Financial\Enums\MoneyUnit;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\Money;
use App\Domain\Financial\ValueObjects\TraceId;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FinancialValueObjectsTest extends TestCase
{
    #[Test]
    public function exact_decimal_normalizes_plain_decimal_strings_without_float(): void
    {
        $value = ExactDecimal::fromString('00012.3400');

        self::assertSame('12.34', $value->value());
        self::assertSame(2, $value->scale());
        self::assertSame('12.35', $value->add(ExactDecimal::fromString('0.01'))->value());
        self::assertSame('12.33', $value->subtract(ExactDecimal::fromString('0.01'))->value());
    }

    #[Test]
    public function exact_decimal_rejects_scientific_notation_and_non_decimal_values(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ExactDecimal::fromString('1e3');
    }

    #[Test]
    public function money_operations_require_the_same_explicit_unit(): void
    {
        $rial = Money::fromString('100', MoneyUnit::Rial);
        $moreRial = Money::fromString('25', MoneyUnit::Rial);

        self::assertSame('125', $rial->add($moreRial)->amount()->value());
        self::assertSame(MoneyUnit::Rial, $rial->unit());

        $this->expectException(InvalidArgumentException::class);

        $rial->add(Money::fromString('10', MoneyUnit::Toman));
    }

    #[Test]
    public function financial_asset_identifier_keeps_type_and_external_identifier_separate(): void
    {
        $asset = new FinancialAssetId(FinancialAssetType::Coin, '16');

        self::assertSame(FinancialAssetType::Coin, $asset->type());
        self::assertSame('16', $asset->externalId());
        self::assertSame('coin:16', (string) $asset);
    }

    #[Test]
    public function trace_and_correlation_ids_are_uuid_based(): void
    {
        $trace = TraceId::generate();
        $correlation = CorrelationId::generate();

        self::assertTrue(TraceId::fromString($trace->value())->equals($trace));
        self::assertTrue(CorrelationId::fromString($correlation->value())->equals($correlation));
    }

    #[Test]
    public function idempotency_key_is_non_empty_and_preserves_the_caller_value(): void
    {
        $key = IdempotencyKey::fromString('order:123:submit');

        self::assertSame('order:123:submit', $key->value());
        self::assertTrue($key->equals(IdempotencyKey::fromString('order:123:submit')));
    }
}
