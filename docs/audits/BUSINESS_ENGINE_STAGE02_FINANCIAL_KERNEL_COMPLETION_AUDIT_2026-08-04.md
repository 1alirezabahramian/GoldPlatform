# Business Engine Stage 02 — Financial Kernel Completion Audit

Date: 2026-08-04
Branch: `work/business-engine-stage02-financial-kernel`

## Status

Financial Kernel foundation is implementation-complete for use as the persistence and atomic-posting base of later engines, subject to the explicit open business rules below.

## Implemented and tested scope

### Domain boundaries

- Money, Gold, Coin and Currency are financial assets.
- Custody is excluded from financial balance aggregation.
- Rial and Toman are explicit units with no hidden conversion.
- Exact decimal arithmetic rejects float input.

### Traceability and safety

- TraceId and CorrelationId are UUID-based.
- Caller-provided IdempotencyKey is mandatory for posting.
- FinancialScope requires Tenant and optionally Company and Branch.
- Repository reads and writes are tenant-scoped.
- Non-scoped repository contracts are deprecated.

### Ledger and journal

- Balanced double-entry Journal invariant is enforced independently per exact asset identity.
- Journal lifecycle supports Draft, Posted and Reversed.
- Reversal creates a new journal and does not mutate financial lines in place.
- Journal rehydration restores persisted lifecycle state without Reflection.

### Persistence

Database-backed implementations exist for:

- TenantScopedJournalRepository
- TenantScopedFinancialEventStore
- TenantScopedIdempotencyRegistry
- TenantScopedBalanceProjectionRepository

The new schema stores:

- financial_journals
- financial_journal_lines
- financial_events
- financial_idempotency_records
- financial_balance_projections

Legacy `financial_transactions` and `ledger_entries` remain unchanged pending an approved migration plan.

### Atomic posting

The tenant-scoped posting flow performs, inside a database transaction:

1. tenant-scoped idempotency lookup;
2. tenant-scoped concurrency lock;
3. journal posting and persistence;
4. projection contract invocation;
5. financial event append;
6. idempotency result claim.

Tests cover successful posting, replay, request conflict, rollback, tenant isolation and lock contention.

## Explicitly open and not guessed

The following are not complete because approved business or Kimia rules are still required:

1. Business-specific debit and credit direction for each operation.
2. Balance projection effect of every journal line.
3. Credit-limit enforcement and negative available-balance authorization.
4. Asset-specific persisted decimal scale, if a numeric database representation is later required.
5. Rial/Toman conversion service and authorized conversion boundaries.
6. Chart of Accounts and ledger-account mapping.
7. Kimia write payloads, voucher mappings and posting timing.
8. Legacy financial data migration and reconciliation.
9. Production Redis multi-process concurrency test.
10. MySQL integration test in addition to the current SQLite CI migration gate.

## Stage decision

Stage 02 can be treated as complete for the Financial Kernel foundation.

Stage 03 may begin with Trading Engine contracts that do not invent pricing formulas or accounting mappings:

- Quote identity and lifecycle;
- expiry and freeze timestamps;
- order state machine;
- validation contracts;
- approval and rejection contracts;
- idempotent command boundaries;
- financial posting ports.

Trading-to-ledger mappings must remain blocked until their business rules are sourced and approved.
