<?php

namespace Tests\Unit\Finance;

use App\Models\FinancialTransaction;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FinancialTransactionModelTest extends TestCase
{
    #[Test]
    public function it_generates_a_uuid_when_one_is_not_supplied(): void
    {
        $transaction = new FinancialTransaction([
            'type' => 'test',
            'status' => 'pending',
        ]);

        $transaction->save();

        $this->assertNotNull($transaction->uuid);
        $this->assertTrue(Str::isUuid($transaction->uuid));
    }

    #[Test]
    public function reference_is_a_polymorphic_relation(): void
    {
        $transaction = new FinancialTransaction();

        $this->assertInstanceOf(MorphTo::class, $transaction->reference());
    }
}
