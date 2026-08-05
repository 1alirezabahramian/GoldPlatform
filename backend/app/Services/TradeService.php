<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Trade;
use App\Services\Order\OrderStateMachine;
use App\Services\Settlement\SettlementService;
use InvalidArgumentException;
use LogicException;

class TradeService
{
    public function __construct(
        private readonly FinancialTransactionService $financialTransactionService,
        private readonly LedgerService $ledgerService,
        private readonly SettlementService $settlementService,
        private readonly OrderStateMachine $orderStateMachine
    ) {
    }

    public function execute(
        Order $order,
        int $fromAccountId,
        int $toAccountId,
        string $ledgerAssetUnit,
        ?string $kimiaReference = null
    ): Trade {
        $this->assertExecutionArguments($fromAccountId, $toAccountId, $ledgerAssetUnit);

        throw new LogicException(
            'Customer financial trade execution is blocked until verified Kimia write and result evidence are implemented. Internal ledger posting cannot authorize or complete the trade.'
        );
    }

    private function assertExecutionArguments(
        int $fromAccountId,
        int $toAccountId,
        string $ledgerAssetUnit
    ): void {
        if ($fromAccountId <= 0 || $toAccountId <= 0) {
            throw new InvalidArgumentException('Trading requires explicit positive ledger account ids.');
        }

        if ($fromAccountId === $toAccountId) {
            throw new InvalidArgumentException('Trading ledger accounts must be different.');
        }

        $ledgerAssetUnit = trim($ledgerAssetUnit);

        if ($ledgerAssetUnit === '' || strlen($ledgerAssetUnit) > 20) {
            throw new InvalidArgumentException('Trading requires an explicit ledger asset unit.');
        }
    }
}
