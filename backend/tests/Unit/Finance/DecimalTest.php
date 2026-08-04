<?php

namespace Tests\Unit\Finance;

use App\Support\Decimal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DecimalTest extends TestCase
{
    #[Test]
    public function it_calculates_exact_financial_values_without_float_or_bcmath(): void
    {
        $this->assertSame('3.75000000', Decimal::add('1.25000000', '2.50000000'));
        $this->assertSame('-1.25000000', Decimal::subtract('1.25000000', '2.50000000'));
        $this->assertSame('1250000.00', Decimal::multiply('1.250', '1000000', 2));
        $this->assertSame(0, Decimal::compare('1.00000000', '1'));
    }
}
