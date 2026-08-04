<?php

namespace Tests\Unit\Enums;

use App\Enums\SettlementStatus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SettlementStatusTest extends TestCase
{
    #[Test]
    public function it_exposes_the_supported_settlement_states(): void
    {
        $this->assertSame(
            ['pending', 'processing', 'completed', 'failed', 'cancelled'],
            array_map(
                static fn (SettlementStatus $status): string => $status->value,
                SettlementStatus::cases()
            )
        );
    }
}
