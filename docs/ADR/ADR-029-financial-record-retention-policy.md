# ADR-029 — Financial Record Retention Policy

## Status
Accepted for application behavior; database foreign-key remediation remains pending explicit migration review.

## Context
GoldPlatform treats the Ledger and approved financial documents as the financial source of truth. Current database foreign keys allow `ledger_entries` to be removed through cascade deletion when a related `financial_transaction` or `wallet_account` is deleted. That behavior can destroy audit evidence.

## Decision
Financial records are append-only from the application perspective.

The following records must never be directly deleted by Controllers, Services, Commands, or Jobs:

- `FinancialTransaction`
- `LedgerEntry`
- `WalletAccount` when it has financial history

Corrections must be represented by one of these mechanisms:

1. a reversing financial transaction;
2. an explicit status transition such as cancelled or reversed, once its contract is approved;
3. archival or deactivation for operational records such as wallet accounts.

Direct balance mutation remains prohibited. Balances must be derived from or reconciled with approved Ledger events.

## Current enforcement
An architecture test scans application execution layers and fails if direct `delete()` or `forceDelete()` calls are introduced for protected financial records.

## Pending database remediation
The existing `cascadeOnDelete()` constraints require a dedicated, reversible migration after these items are verified:

- production and test database state;
- existing orphan records;
- exact foreign-key names on MySQL;
- approved retention behavior for users and wallets;
- rollback procedure;
- backup and restore test.

Until that migration is approved and tested, no destructive migration will be created.

## Consequences
- Financial history remains auditable at the application layer.
- Corrections become explicit events instead of silent mutation or deletion.
- Database-level cascade risk is documented but not silently changed.
- User deletion must eventually become anonymization/deactivation rather than destruction of financial history.
