<?php

namespace Tests\Feature\Settlement;

use App\Enums\SettlementStatus;
use App\Models\FinancialTransaction;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletAccount;
use App\Services\LedgerService;
use App\Services\Settlement\SettlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SettlementLedgerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_completes_only_after_a_balanced_financial_transaction_is_linked(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::query()->create(['user_id' => $user->id]);

        $source = WalletAccount::query()->create([
            'wallet_id' => $wallet->id,
            'code' => 'RIAL',
            'title' => 'ریال',
            'balance' => '0',
            'blocked_balance' => '0',
            'is_active' => true,
        ]);

        $target = WalletAccount::query()->create([
            'wallet_id' => $wallet->id,
            'code' => 'GOLD18',
            'title' => 'طلای ۱۸ عیار',
            'balance' => '0',
            'blocked_balance' => '0',
            'is_active' => true,
        ]);

        $order = Order::query()->create([
            'user_id' => $user->id,
            'type' => 'buy',
            'status' => 'approved',
            'gold_weight' => '1.250',
            'gold_price' => '1000000',
            'commission' => '0',
            'total_price' => '1250000',
        ]);

        $transaction = FinancialTransaction::query()->create([
            'type' => 'settlement',
            'status' => 'pending',
            'reference_type' => Order::class,
            'reference_id' => $order->id,
        ]);

        app(LedgerService::class)->transfer(
            transaction: $transaction,
            fromAccountId: $source->id,
            toAccountId: $target->id,
            amount: '1250000',
            currency: 'IRR'
        );

        $service = app(SettlementService::class);
        $settlement = $service->createPending(
            order: $order,
            assetType: 'money',
            amount: '1250000',
            idempotencyKey: 'settlement:ledger:balanced'
        );

        $settlement = $service->attachFinancialTransaction($settlement, $transaction);
        $settlement = $service->startProcessing($settlement);
        $settlement = $service->completeWithLedger($settlement);

        $this->assertSame(SettlementStatus::Completed, $settlement->status);
        $this->assertSame($transaction->id, $settlement->financial_transaction_id);
    }

    #[Test]
    public function it_rejects_completion_when_the_linked_transaction_is_not_balanced(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::query()->create(['user_id' => $user->id]);

        $account = WalletAccount::query()->create([
            'wallet_id' => $wallet->id,
            'code' => 'RIAL',
            'title' => 'ریال',
            'balance' => '0',
            'blocked_balance' => '0',
            'is_active' => true,
        ]);

        $order = Order::query()->create([
            'user_id' => $user->id,
            'type' => 'buy',
            'status' => 'approved',
            'gold_weight' => '1.000',
            'gold_price' => '1000000',
            'commission' => '0',
            'total_price' => '1000000',
        ]);

        $transaction = FinancialTransaction::query()->create([
            'type' => 'settlement',
            'status' => 'pending',
            'reference_type' => Order::class,
            'reference_id' => $order->id,
        ]);

        app(LedgerService::class)->createEntry(
            transaction: $transaction,
            walletAccountId: $account->id,
            entryType: 'debit',
            amount: '1000000',
            currency: 'IRR'
        );

        $service = app(SettlementService::class);
        $settlement = $service->createPending(
            order: $order,
            assetType: 'money',
            amount: '1000000',
            idempotencyKey: 'settlement:ledger:unbalanced'
        );

        $settlement = $service->attachFinancialTransaction($settlement, $transaction);
        $settlement = $service->startProcessing($settlement);

        $this->expectException(LogicException::class);
        $service->completeWithLedger($settlement);
    }
}
