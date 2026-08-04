<?php

namespace App\Infrastructure\Financial\Database;

use App\Domain\Financial\Contracts\TenantScopedFinancialEventStore;
use App\Domain\Financial\Events\FinancialEvent;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class DatabaseTenantScopedFinancialEventStore implements TenantScopedFinancialEventStore
{
    public function append(FinancialScope $scope, FinancialEvent $event): void
    {
        DB::table('financial_events')->insert([
            'event_uuid' => (string) Str::uuid(),
            ...$this->scopeColumns($scope),
            'name' => $event->name(),
            'trace_id' => $event->traceId()->value(),
            'correlation_id' => $event->correlationId()->value(),
            'idempotency_key' => $event->idempotencyKey()->value(),
            'payload' => json_encode($event->payload(), JSON_THROW_ON_ERROR),
            'occurred_at' => $event->occurredAt()->format('Y-m-d H:i:s.u'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function byCorrelationId(FinancialScope $scope, CorrelationId $correlationId): array
    {
        return DB::table('financial_events')
            ->where('scope_hash', $this->scopeHash($scope))
            ->where('correlation_id', $correlationId->value())
            ->orderBy('id')
            ->get()
            ->map(static fn (object $row): FinancialEvent => new FinancialEvent(
                name: $row->name,
                traceId: TraceId::fromString($row->trace_id),
                correlationId: CorrelationId::fromString($row->correlation_id),
                idempotencyKey: IdempotencyKey::fromString($row->idempotency_key),
                occurredAt: new DateTimeImmutable($row->occurred_at),
                payload: $row->payload === null ? [] : json_decode($row->payload, true, 512, JSON_THROW_ON_ERROR),
            ))
            ->all();
    }

    /** @return array<string, string|null> */
    private function scopeColumns(FinancialScope $scope): array
    {
        return [
            'scope_key' => $scope->key(),
            'scope_hash' => $this->scopeHash($scope),
            'tenant_id' => $scope->tenantId(),
            'company_id' => $scope->companyId(),
            'branch_id' => $scope->branchId(),
        ];
    }

    private function scopeHash(FinancialScope $scope): string
    {
        return hash('sha256', $scope->key());
    }
}
