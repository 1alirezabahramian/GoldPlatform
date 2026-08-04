<?php

namespace Tests\Unit\Financial;

use App\Domain\Financial\Balance\BalanceSnapshot;
use App\Domain\Financial\Enums\FinancialAssetType;
use App\Domain\Financial\Enums\ReservationStatus;
use App\Domain\Financial\Events\FinancialEvent;
use App\Domain\Financial\Reservation\Reservation;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\LedgerAccountId;
use App\Domain\Financial\ValueObjects\ReservationId;
use App\Domain\Financial\ValueObjects\TraceId;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BalanceReservationEventTest extends TestCase
{
    #[Test]
    public function balance_snapshot_derives_available_without_forbidding_negative_financial_balance(): void
    {
        $snapshot = new BalanceSnapshot(
            LedgerAccountId::fromString('customer:1'),
            new FinancialAssetId(FinancialAssetType::MONEY, 'toman'),
            ExactDecimal::fromString('100'),
            ExactDecimal::fromString('125'),
        );

        self::assertSame('-25', $snapshot->available()->value());
        self::assertSame('100', $snapshot->posted()->value());
        self::assertSame('125', $snapshot->reserved()->value());
    }

    #[Test]
    public function reservation_lifecycle_is_immutable_and_preserves_identity_and_correlation(): void
    {
        $reservation = $this->reservation();
        $released = $reservation->release(
            TraceId::generate(),
            IdempotencyKey::fromString('reservation:release:1'),
        );

        self::assertSame(ReservationStatus::ACTIVE, $reservation->status());
        self::assertSame(ReservationStatus::RELEASED, $released->status());
        self::assertTrue($reservation->id()->equals($released->id()));
        self::assertTrue($reservation->correlationId()->equals($released->correlationId()));
    }

    #[Test]
    public function terminal_reservation_cannot_transition_again(): void
    {
        $released = $this->reservation()->release(
            TraceId::generate(),
            IdempotencyKey::fromString('reservation:release:2'),
        );

        $this->expectException(DomainException::class);

        $released->capture(
            TraceId::generate(),
            IdempotencyKey::fromString('reservation:capture:2'),
        );
    }

    #[Test]
    public function financial_event_carries_required_traceability_context(): void
    {
        $trace = TraceId::generate();
        $correlation = CorrelationId::generate();
        $idempotency = IdempotencyKey::fromString('event:journal-posted:1');
        $occurredAt = new DateTimeImmutable('2026-08-04T12:00:00+00:00');

        $event = new FinancialEvent(
            name: 'financial.journal.posted',
            traceId: $trace,
            correlationId: $correlation,
            idempotencyKey: $idempotency,
            occurredAt: $occurredAt,
            payload: ['journal_id' => 'journal-1'],
        );

        self::assertSame('financial.journal.posted', $event->name());
        self::assertTrue($trace->equals($event->traceId()));
        self::assertTrue($correlation->equals($event->correlationId()));
        self::assertSame('journal-1', $event->payload()['journal_id']);
    }

    private function reservation(): Reservation
    {
        return new Reservation(
            id: ReservationId::generate(),
            accountId: LedgerAccountId::fromString('customer:1'),
            assetId: new FinancialAssetId(FinancialAssetType::GOLD, '750'),
            amount: ExactDecimal::fromString('1.25'),
            traceId: TraceId::generate(),
            correlationId: CorrelationId::generate(),
            idempotencyKey: IdempotencyKey::fromString('reservation:create:1'),
        );
    }
}
