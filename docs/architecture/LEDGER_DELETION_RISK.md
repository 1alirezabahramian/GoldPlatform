# Ledger Deletion Risk Review

## Status
Open blocker — no schema change applied

## Confirmed current risk
The existing `ledger_entries` migration uses cascading foreign-key deletion from both:

- `financial_transaction_id`
- `wallet_account_id`

This means deletion of a financial transaction or wallet account can automatically delete historical ledger entries.

## Why this matters
GoldPlatform defines the Ledger and confirmed financial documents as the financial source of truth. Silent deletion of ledger history conflicts with auditability, traceability, reconciliation, and recovery expectations.

## Current action
No migration has been changed because altering foreign keys on existing financial data is a schema and operational decision that requires:

- confirmation of current production/test data,
- an approved retention and reversal policy,
- a safe migration and rollback plan,
- tests for account archival and transaction reversal,
- review of all code paths that delete users, wallets, accounts, or financial transactions.

## Temporary boundary
Until a dedicated ADR is approved and migration safety is validated:

- financial execution remains disabled,
- no code should delete FinancialTransaction, LedgerEntry, or funded WalletAccount records,
- corrections must not be implemented by deleting ledger history,
- direct balance mutation remains prohibited.
