<?php

namespace Tests\Unit\Architecture;

use App\Models\WalletAccount;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WalletAccountSerializationBoundaryTest extends TestCase
{
    #[Test]
    public function internal_balance_projection_fields_are_hidden_from_serialization(): void
    {
        $account = new WalletAccount([
            'code' => 'INTERNAL',
            'title' => 'Internal projection',
            'balance' => '125.00000000',
            'blocked_balance' => '25.00000000',
            'is_active' => true,
        ]);

        $serialized = $account->toArray();

        $this->assertArrayNotHasKey('balance', $serialized);
        $this->assertArrayNotHasKey('blocked_balance', $serialized);
        $this->assertArrayNotHasKey('available_balance', $serialized);
        $this->assertSame('INTERNAL', $serialized['code']);
    }
}
