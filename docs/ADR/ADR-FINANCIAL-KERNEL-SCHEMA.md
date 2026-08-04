# ADR — Tenant-scoped Financial Kernel Schema

Status: Accepted
Date: 2026-08-04

## Context

The repository already contains legacy `financial_transactions` and `ledger_entries` tables. Those tables are not tenant-scoped, the transaction UUID column is unnamed, and the legacy ledger amount is fixed to `decimal(24,6)`. The approved Financial Kernel requires explicit tenant isolation, traceability, idempotency, immutable journals, financial events, and exact decimal handling without inventing an asset scale.

The legacy tables are not deleted or rewritten in this stage because a data migration and compatibility plan has not yet been approved.

## Decision

Create an independent tenant-scoped persistence schema:

- `financial_journals`
- `financial_journal_lines`
- `financial_events`
- `financial_idempotency_records`
- `financial_balance_projections`

Every tenant-scoped table stores:

- `scope_key`: readable canonical scope value;
- `scope_hash`: SHA-256 hash used for fixed-length indexes;
- `tenant_id`;
- nullable `company_id`;
- nullable `branch_id`.

Financial documents use an internal numeric relational key plus immutable UUID business identifiers.

Exact financial amounts are stored as canonical decimal strings. This prevents float usage and avoids inventing a shared database scale for Money, Gold, Coin and Currency before an approved per-asset precision policy exists.

Journal lines reference journals with a restrictive delete rule. Posted-document immutability remains enforced by the Domain and repository layers; no cascade deletion is allowed for journal lines.

## Indexing decision

Raw scope identifiers can be long and may exceed MySQL composite-index byte limits. Therefore uniqueness and lookup indexes use the fixed-length `scope_hash`, while the readable `scope_key` and individual scope identifiers remain stored for audit and diagnostics.

Repository adapters must calculate `scope_hash` as:

```text
sha256(FinancialScope::key())
```

## Legacy compatibility

The following legacy tables remain untouched in this stage:

- `financial_transactions`
- `ledger_entries`

They are not the persistence target for new Financial Kernel development. Their migration or retirement requires a separate audited plan.

## Explicitly not decided here

- Per-asset decimal scale or rounding policy.
- Business-specific debit/credit direction.
- Persisted Tenant, Company and Branch foreign-key models.
- Wallet mutation rules.
- Kimia write mappings.
- Legacy financial data migration.

## Verification

The schema must pass:

- PHP syntax checks;
- `migrate:fresh` on the CI database;
- table and scope-column assertions;
- exact decimal round-trip without truncation;
- same idempotency key isolation across distinct tenants.
