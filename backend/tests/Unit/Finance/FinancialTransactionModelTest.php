<?php

namespace Tests\Unit\Finance;

use App\Models\FinancialTransaction;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FinancialTransactionModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_generates_a_uuid_when_one_is_not_supplied(): void
    {
        $transaction = FinancialTransaction::create([
            'type' => 'test',
            'status' => 'pending',
            'reference_type' => FinancialTransaction::class,
            'reference_id' => 1,
        ]);

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
