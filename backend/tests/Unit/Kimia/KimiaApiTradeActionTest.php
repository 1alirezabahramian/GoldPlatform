<?php

namespace Tests\Unit\Kimia;

use App\Enums\KimiaApiTradeAction;
use App\Enums\KimiaTradeSide;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class KimiaApiTradeActionTest extends TestCase
{
    #[Test]
    public function customer_buy_maps_to_kimia_api_sell_action_64(): void
    {
        $tradeSide = KimiaTradeSide::fromCustomerOrderType('buy');
        $action = KimiaApiTradeAction::fromTradeSide($tradeSide);

        $this->assertSame(KimiaApiTradeAction::SellToCustomer, $action);
        $this->assertSame(64, $action->value);
    }

    #[Test]
    public function customer_sell_maps_to_kimia_api_buy_action_32(): void
    {
        $tradeSide = KimiaTradeSide::fromCustomerOrderType('sell');
        $action = KimiaApiTradeAction::fromTradeSide($tradeSide);

        $this->assertSame(KimiaApiTradeAction::BuyFromCustomer, $action);
        $this->assertSame(32, $action->value);
    }

    #[Test]
    public function operational_form_codes_are_not_valid_api_trade_actions(): void
    {
        $this->assertNull(KimiaApiTradeAction::tryFrom(3));
        $this->assertNull(KimiaApiTradeAction::tryFrom(4));
    }
}
