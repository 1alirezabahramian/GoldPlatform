<?php

namespace Tests\Unit\Domain;

use App\Models\LedgerEntry;
use App\Models\Order;
use App\Models\Trade;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FinancialPrecisionTest extends TestCase
{
    #[Test]
    public function order_financial_values_are_cast_as_exact_decimal_strings(): void
    {
        $order = new Order([
            'gold_weight' => '1.2345',
            'gold_price' => '187000000.4',
            'commission' => '1234.5',
            'total_price' => '187001234.5',
        ]);

        $this->assertSame('1.235', $order->gold_weight);
        $this->assertSame('187000000', $order->gold_price);
        $this->assertSame('1235', $order->commission);
        $this->assertSame('187001235', $order->total_price);
    }

    #[Test]
    public function trade_values_are_cast_as_exact_decimal_strings(): void
    {
        $trade = new Trade([
            'quantity' => '0.1234567',
            'unit_price' => '187000000.125',
            'total_amount' => '23000000.999',
        ]);

        $this->assertSame('0.123457', $trade->quantity);
        $this->assertSame('187000000.13', $trade->unit_price);
        $this->assertSame('23000001.00', $trade->total_amount);
    }

    #[Test]
    public function ledger_amount_is_cast_as_an_exact_decimal_string(): void
    {
        $entry = new LedgerEntry([
            'amount' => '-0.1234567',
        ]);

        $this->assertSame('-0.123457', $entry->amount);
        $this->assertIsString($entry->amount);
    }
}
