<?php

namespace App\Infrastructure\Trading\Database;

use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use App\Domain\Trading\Contracts\TenantScopedQuoteRepository;
use App\Domain\Trading\Enums\QuoteStatus;
use App\Domain\Trading\Quote\Quote;
use App\Domain\Trading\ValueObjects\QuoteId;
use DateTimeImmutable;
use DomainException;
use Illuminate\Support\Facades\DB;

final class DatabaseTenantScopedQuoteRepository implements TenantScopedQuoteRepository
{
    public function save(FinancialScope $scope, Quote $quote): void
    {
        if ($quote->scope()->key() !== $scope->key()) {
            throw new DomainException('Quote scope does not match repository scope.');
        }

        DB::table('trading_quotes')->updateOrInsert(
            [
                'scope_hash' => hash('sha256', $scope->key()),
                'quote_uuid' => $quote->id()->value(),
            ],
            [
                'scope_key' => $scope->key(),
                'tenant_id' => $scope->tenantId(),
                'company_id' => $scope->companyId(),
                'branch_id' => $scope->branchId(),
                'trace_id' => $quote->traceId()->value(),
                'correlation_id' => $quote->correlationId()->value(),
                'idempotency_key' => $quote->idempotencyKey()->value(),
                'status' => $quote->status()->value,
                'requested_at' => $quote->requestedAt()->format('Y-m-d H:i:s.u'),
                'expires_at' => $quote->expiresAt()?->format('Y-m-d H:i:s.u'),
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );
    }

    public function find(FinancialScope $scope, QuoteId $quoteId): ?Quote
    {
        $row = DB::table('trading_quotes')
            ->where('scope_hash', hash('sha256', $scope->key()))
            ->where('quote_uuid', $quoteId->value())
            ->first();

        if ($row === null) {
            return null;
        }

        return Quote::rehydrate(
            id: QuoteId::fromString($row->quote_uuid),
            scope: $scope,
            traceId: TraceId::fromString($row->trace_id),
            correlationId: CorrelationId::fromString($row->correlation_id),
            idempotencyKey: IdempotencyKey::fromString($row->idempotency_key),
            status: QuoteStatus::from($row->status),
            requestedAt: new DateTimeImmutable($row->requested_at),
            expiresAt: $row->expires_at === null ? null : new DateTimeImmutable($row->expires_at),
        );
    }
}
