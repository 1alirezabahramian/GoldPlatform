# ADR-029 — Ledger Integrity Guards

## Status
Accepted

## Context
GoldPlatform treats the Ledger and confirmed financial documents as the source of truth. The existing LedgerService could accept invalid technical inputs before attempting persistence, including zero or negative transfer amounts, unsupported entry types, transfers between the same wallet account, invalid account identifiers, and unpersisted FinancialTransaction instances.

These conditions are technical integrity failures, not new business rules.

## Decision
LedgerService must reject invalid inputs before writing any LedgerEntry:

- FinancialTransaction must already be persisted.
- Wallet account identifiers must be positive integers.
- Entry type is limited to `debit` or `credit`, matching the current database contract.
- Amount must be numeric and strictly greater than zero.
- Currency is required, normalized to uppercase, and limited to the existing schema length.
- A transfer cannot use the same source and destination account.
- Both sides of a transfer continue to be created inside one database transaction using the same exact amount and currency.

## Explicit non-decisions
This ADR does not define:

- Which wallet accounts participate in a customer trade.
- Whether an asset uses IRR, toman, grams, pieces, or a Kimia currency identifier.
- Debit/credit meaning from the customer or business perspective.
- Settlement, commission, credit-limit, pricing, or Kimia voucher rules.
- A policy for deleting financial records or changing existing foreign-key cascades.

Those items require confirmed domain rules and separate decisions.

## Consequences
- Invalid technical ledger writes fail before database access.
- Existing valid transfer behavior remains atomic.
- No balance is mutated directly.
- Tests protect the integrity boundary while financial execution remains disabled.
