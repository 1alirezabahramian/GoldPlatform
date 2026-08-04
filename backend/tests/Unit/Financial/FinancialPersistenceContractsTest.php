<?php

namespace Tests\Unit\Financial;

use App\Domain\Financial\Balance\BalanceSnapshot;
use App\Domain\Financial\Enums\FinancialAssetType;
use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\LedgerAccountId;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Infrastructure\Financial\InMemory\InMemoryBalanceProjectionRepository;
use App\Infrastructure\Financial\InMemory\InMemoryIdempotencyRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class FinancialPersistenceContractsTest extends TestCase
{
    #[Test]
    public function the_same_idempotency_key_and_request_can_be_replayed_safely(): void
    {
        $registry = new InMemoryIdempotencyRegistry();
        $record = $this->record('request-hash-a');

        $registry->claim($record);
        $registry->claim($record);

        self::assertSame($record, $registry->find($record->key()));
    }

    #[Test]
    public function an_idempotency_key_cannot_be_reused_for_a_different_request(): void
    {
        $registry = new InMemoryIdempotencyRegistry();
        $registry->claim($this->record('request-hash-a'));

        $this->expectException(RuntimeException::class);

        $registry->claim($this->record('request-hash-b'));
    }

    #[Test]
    public function projections_are_isolated_by_account_and_exact_asset(): void
    {
        $repository = new InMemoryBalanceProjectionRepository();
        $account = LedgerAccountId::fromString('customer:1');
        $toman = new FinancialAssetId(FinancialAssetType::MONEY, 'toman');
        $gold = new FinancialAssetId(FinancialAssetType::GOLD, '750');

        $repository->save(new BalanceSnapshot(
            $account,
            $toman,
            ExactDecimal::fromString('1000'),
            ExactDecimal::fromString('100'),
        ));

        self::assertSame('900', $repository->get($account, $toman)?->available()->value());
        self::assertNull($repository->get($account, $gold));
    }

    private function record(string $requestHash): IdempotencyRecord
    {
        return new IdempotencyRecord(
            IdempotencyKey::fromString('order:123:submit'),
            'order.submit',
            $requestHash,
            TraceId::generate(),
            'order:123',
        );
    }
}
