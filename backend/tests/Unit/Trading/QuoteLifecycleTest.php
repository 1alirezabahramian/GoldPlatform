<?php

namespace Tests\Unit\Trading;

use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\Enums\QuoteStatus;
use App\Domain\Trading\Quote\Quote;
use App\Infrastructure\Trading\InMemory\InMemoryTenantScopedQuoteRepository;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

final class QuoteLifecycleTest extends TestCase
{
    public function test_requested_quote_can_be_frozen_used_and_persisted_per_tenant(): void
    {
        $requestedAt = new DateTimeImmutable('2026-08-04T12:00:00+00:00');
        $expiresAt = new DateTimeImmutable('2026-08-04T12:05:00+00:00');
        $scopeA = new FinancialScope('tenant-a', 'company-a', 'branch-a');
        $scopeB = new FinancialScope('tenant-b', 'company-a', 'branch-a');

        $requested = Quote::request(
            $scopeA,
            TraceId::generate(),
            CorrelationId::generate(),
            IdempotencyKey::fromString('quote-request-1'),
            $requestedAt,
        );

        self::assertSame(QuoteStatus::REQUESTED, $requested->status());

        $frozen = $requested->freeze($expiresAt);
        self::assertSame(QuoteStatus::FROZEN, $frozen->status());
        self::assertSame($expiresAt, $frozen->expiresAt());

        $repository = new InMemoryTenantScopedQuoteRepository();
        $repository->save($scopeA, $frozen);

        self::assertSame($frozen, $repository->find($scopeA, $frozen->id()));
        self::assertNull($repository->find($scopeB, $frozen->id()));

        $used = $frozen->use(new DateTimeImmutable('2026-08-04T12:04:59+00:00'));
        self::assertSame(QuoteStatus::USED, $used->status());
    }

    public function test_quote_cannot_be_used_at_or_after_expiration(): void
    {
        $requestedAt = new DateTimeImmutable('2026-08-04T12:00:00+00:00');
        $expiresAt = new DateTimeImmutable('2026-08-04T12:05:00+00:00');
        $quote = $this->requested($requestedAt)->freeze($expiresAt);

        $this->expectException(DomainException::class);
        $quote->use($expiresAt);
    }

    public function test_quote_can_expire_only_after_its_expiration_time(): void
    {
        $requestedAt = new DateTimeImmutable('2026-08-04T12:00:00+00:00');
        $expiresAt = new DateTimeImmutable('2026-08-04T12:05:00+00:00');
        $quote = $this->requested($requestedAt)->freeze($expiresAt);

        $expired = $quote->expire($expiresAt);
        self::assertSame(QuoteStatus::EXPIRED, $expired->status());
    }

    public function test_terminal_quote_cannot_be_cancelled(): void
    {
        $requestedAt = new DateTimeImmutable('2026-08-04T12:00:00+00:00');
        $quote = $this->requested($requestedAt)
            ->freeze(new DateTimeImmutable('2026-08-04T12:05:00+00:00'))
            ->use(new DateTimeImmutable('2026-08-04T12:04:00+00:00'));

        $this->expectException(DomainException::class);
        $quote->cancel();
    }

    private function requested(DateTimeImmutable $requestedAt): Quote
    {
        return Quote::request(
            new FinancialScope('tenant-a'),
            TraceId::generate(),
            CorrelationId::generate(),
            IdempotencyKey::fromString('quote-request-test'),
            $requestedAt,
        );
    }
}
