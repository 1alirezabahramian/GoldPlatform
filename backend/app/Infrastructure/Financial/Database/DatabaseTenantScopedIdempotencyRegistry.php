<?php

namespace App\Infrastructure\Financial\Database;

use App\Domain\Financial\Contracts\TenantScopedIdempotencyRegistry;
use App\Domain\Financial\Idempotency\IdempotencyRecord;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\TraceId;
use DomainException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

final class DatabaseTenantScopedIdempotencyRegistry implements TenantScopedIdempotencyRegistry
{
    public function find(FinancialScope $scope, IdempotencyKey $key): ?IdempotencyRecord
    {
        $row = DB::table('financial_idempotency_records')
            ->where('scope_hash', $this->scopeHash($scope))
            ->where('idempotency_key', $key->value())
            ->first();

        if ($row === null) {
            return null;
        }

        return new IdempotencyRecord(
            key: IdempotencyKey::fromString($row->idempotency_key),
            operation: $row->operation,
            requestHash: $row->request_hash,
            traceId: TraceId::fromString($row->trace_id),
            resultReference: $row->result_reference,
        );
    }

    public function claim(FinancialScope $scope, IdempotencyRecord $record): void
    {
        $existing = $this->find($scope, $record->key());

        if ($existing !== null) {
            if (! $existing->matches($record->operation(), $record->requestHash())) {
                throw new DomainException('Idempotency key was already used for a different request in this financial scope.');
            }

            return;
        }

        try {
            DB::table('financial_idempotency_records')->insert([
                'scope_key' => $scope->key(),
                'scope_hash' => $this->scopeHash($scope),
                'tenant_id' => $scope->tenantId(),
                'company_id' => $scope->companyId(),
                'branch_id' => $scope->branchId(),
                'idempotency_key' => $record->key()->value(),
                'operation' => $record->operation(),
                'request_hash' => $record->requestHash(),
                'trace_id' => $record->traceId()->value(),
                'result_reference' => $record->resultReference(),
                'completed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (QueryException $exception) {
            $existing = $this->find($scope, $record->key());

            if ($existing === null || ! $existing->matches($record->operation(), $record->requestHash())) {
                throw $exception;
            }
        }
    }

    private function scopeHash(FinancialScope $scope): string
    {
        return hash('sha256', $scope->key());
    }
}
