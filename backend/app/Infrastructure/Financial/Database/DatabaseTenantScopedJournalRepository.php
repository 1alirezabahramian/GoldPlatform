<?php

namespace App\Infrastructure\Financial\Database;

use App\Domain\Financial\Contracts\TenantScopedJournalRepository;
use App\Domain\Financial\Enums\FinancialAssetType;
use App\Domain\Financial\Enums\JournalSide;
use App\Domain\Financial\Enums\JournalStatus;
use App\Domain\Financial\Journal\Journal;
use App\Domain\Financial\Journal\JournalDocument;
use App\Domain\Financial\Journal\JournalLine;
use App\Domain\Financial\ValueObjects\CorrelationId;
use App\Domain\Financial\ValueObjects\ExactDecimal;
use App\Domain\Financial\ValueObjects\FinancialAssetId;
use App\Domain\Financial\ValueObjects\FinancialScope;
use App\Domain\Financial\ValueObjects\IdempotencyKey;
use App\Domain\Financial\ValueObjects\LedgerAccountId;
use App\Domain\Financial\ValueObjects\TraceId;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class DatabaseTenantScopedJournalRepository implements TenantScopedJournalRepository
{
    public function save(FinancialScope $scope, JournalDocument $document): void
    {
        $journal = $document->journal();
        $scopeKey = $scope->key();
        $scopeHash = hash('sha256', $scopeKey);
        $now = now();

        DB::transaction(function () use ($scope, $scopeKey, $scopeHash, $document, $journal, $now): void {
            $existing = DB::table('financial_journals')
                ->where('scope_hash', $scopeHash)
                ->where('trace_id', $journal->traceId()->value())
                ->first();

            $attributes = [
                'scope_key' => $scopeKey,
                'scope_hash' => $scopeHash,
                'tenant_id' => $scope->tenantId(),
                'company_id' => $scope->companyId(),
                'branch_id' => $scope->branchId(),
                'trace_id' => $journal->traceId()->value(),
                'correlation_id' => $journal->correlationId()->value(),
                'idempotency_key' => $journal->idempotencyKey()->value(),
                'status' => $document->status()->value,
                'reversal_trace_id' => $document->reversalTraceId()?->value(),
                'metadata' => json_encode(['description' => $journal->description()], JSON_THROW_ON_ERROR),
                'posted_at' => $document->status() === JournalStatus::POSTED ? $now : null,
                'updated_at' => $now,
            ];

            if ($existing === null) {
                $journalId = DB::table('financial_journals')->insertGetId([
                    'document_uuid' => (string) Str::uuid(),
                    ...$attributes,
                    'created_at' => $now,
                ]);
            } else {
                $journalId = (int) $existing->id;
                DB::table('financial_journals')->where('id', $journalId)->update($attributes);
                DB::table('financial_journal_lines')->where('financial_journal_id', $journalId)->delete();
            }

            foreach ($journal->lines() as $line) {
                DB::table('financial_journal_lines')->insert([
                    'financial_journal_id' => $journalId,
                    'ledger_account_id' => $line->accountId()->value(),
                    'asset_type' => $line->assetId()->type()->value,
                    'asset_id' => $line->assetId()->externalId(),
                    'side' => $line->side()->value,
                    'amount' => $line->amount()->value(),
                    'description' => $line->description(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });
    }

    public function findByTraceId(FinancialScope $scope, TraceId $traceId): ?JournalDocument
    {
        $row = DB::table('financial_journals')
            ->where('scope_hash', hash('sha256', $scope->key()))
            ->where('trace_id', $traceId->value())
            ->first();

        if ($row === null) {
            return null;
        }

        $lines = DB::table('financial_journal_lines')
            ->where('financial_journal_id', $row->id)
            ->orderBy('id')
            ->get()
            ->map(static fn (object $line): JournalLine => new JournalLine(
                accountId: LedgerAccountId::fromString($line->ledger_account_id),
                assetId: new FinancialAssetId(
                    FinancialAssetType::from($line->asset_type),
                    $line->asset_id,
                ),
                side: JournalSide::from($line->side),
                amount: ExactDecimal::fromString($line->amount),
                description: $line->description,
            ))
            ->all();

        $metadata = $row->metadata === null
            ? []
            : json_decode($row->metadata, true, 512, JSON_THROW_ON_ERROR);

        $journal = new Journal(
            traceId: TraceId::fromString($row->trace_id),
            correlationId: CorrelationId::fromString($row->correlation_id),
            idempotencyKey: IdempotencyKey::fromString($row->idempotency_key),
            lines: $lines,
            description: $metadata['description'] ?? null,
        );

        return JournalDocument::rehydrate(
            journal: $journal,
            status: JournalStatus::from($row->status),
            reversalTraceId: $row->reversal_trace_id === null
                ? null
                : TraceId::fromString($row->reversal_trace_id),
        );
    }
}
