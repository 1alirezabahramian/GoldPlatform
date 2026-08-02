<?php

namespace Tests\Unit\Kimia;

use App\Enums\KimiaTradeSide;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class KimiaTradeSideTest extends TestCase
{
    #[Test]
    public function customer_buy_maps_to_business_sell_side(): void
    {
        $side = KimiaTradeSide::fromCustomerOrderType('buy');

        $this->assertSame(KimiaTradeSide::SellToCustomer, $side);
    }

    #[Test]
    public function customer_sell_maps_to_business_buy_side(): void
    {
        $side = KimiaTradeSide::fromCustomerOrderType('sell');

        $this->assertSame(KimiaTradeSide::BuyFromCustomer, $side);
    }

    #[Test]
    public function unsupported_customer_order_type_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        KimiaTradeSide::fromCustomerOrderType('exchange');
    }
}
