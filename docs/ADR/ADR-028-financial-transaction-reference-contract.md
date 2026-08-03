# ADR-028 — Financial Transaction Reference Contract

- **Status:** Accepted
- **Date:** 2026-08-03
- **Scope:** FinancialTransaction, Trade, LedgerEntry

## Context

`financial_transactions` stores `reference_type` and `reference_id`, which represents a polymorphic reference to the business event that caused the financial transaction. The model previously exposed a `trade()` relation that always interpreted `reference_id` as a Trade ID and ignored `reference_type`.

That behavior was incompatible with future approved references such as orders, settlements, custody operations, deliveries, or other auditable financial events.

The model also exposed a `uuid` field but did not guarantee generation when callers omitted it.

## Decision

- `FinancialTransaction::reference()` is the canonical polymorphic relation.
- The model generates a UUID during creation only when a UUID was not already supplied.
- Existing caller-supplied UUIDs are preserved.
- `LedgerEntry` remains linked to `FinancialTransaction` through `financial_transaction_id`.
- No financial amount, status transition, ledger posting rule, or Kimia write behavior is changed by this decision.

## Safety Constraints

- `TradeService` remains blocked from HTTP execution.
- No hard-coded wallet account IDs may be used in a production posting path.
- Ledger posting requires a separately approved contract for account resolution, balancing, idempotency, audit, and failure recovery.
- Financial and custody references must not be merged into one simplified balance model.

## Tests

Automated tests verify:

- UUID generation when omitted
- the polymorphic type of the canonical `reference()` relation
