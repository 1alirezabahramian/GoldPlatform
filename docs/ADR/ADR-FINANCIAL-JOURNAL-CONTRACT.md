# ADR — Financial Journal Contract

**Date:** 2026-08-04  
**Status:** Proposed in Stage 02 / Slice 03

## Context

GoldPlatform requires an immutable, traceable and exact financial journal before Wallet, Trading or Settlement may change balances.

The legacy `LedgerService` writes database rows directly, uses a generic currency string and does not validate a complete balanced journal before persistence. This ADR does not remove or replace that path yet; it defines the domain contract that a future posting adapter must satisfy.

## Decision

1. A Journal is an immutable domain aggregate.
2. Every Journal carries:
   - TraceId
   - CorrelationId
   - IdempotencyKey
   - at least two JournalLine values
3. Every JournalLine carries:
   - opaque LedgerAccountId
   - exact FinancialAssetId
   - JournalSide (`debit` or `credit`)
   - positive ExactDecimal amount
4. Balance is validated independently for every exact FinancialAssetId.
5. Money, Gold, Coin and Currency totals are never mixed.
6. Different Money units or different Kimia-backed identifiers cannot balance each other.
7. A reversal creates a new Journal with:
   - a new TraceId
   - a new IdempotencyKey
   - the original CorrelationId
   - every line side reversed
8. This Slice does not persist journals and does not mutate wallet balances.

## Consequences

- A mixed asset trade must contain balanced lines for each involved asset identity.
- A Toman debit cannot be balanced by Rial, Gold, Coin or Currency credit.
- Persistence, posting status, tenant ownership and account chart mapping remain separate decisions.
- Existing database models and services are not yet production-safe merely because this contract exists.

## Explicitly Not Decided

- Accounting meaning of each business operation
- Customer/account debit-credit direction
- Chart of accounts
- Tenant boundary
- Decimal database scale
- Posting timing relative to Kimia
- Database schema and migration
- Wallet projection behavior
- Kimia write mapping

## Verification

Unit tests must prove:

- balanced single-asset journals pass
- unbalanced journals fail
- each asset balances independently
- one asset cannot offset another
- zero or negative line amounts fail
- reversal flips all sides while preserving correlation
