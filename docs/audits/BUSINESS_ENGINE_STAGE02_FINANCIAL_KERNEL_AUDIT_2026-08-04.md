# Business Engine Stage 02 — Financial Kernel Audit

Date: 2026-08-04
Branch: `work/business-engine-stage02-financial-kernel`
Status: Started

## Confirmed ground truth

- Financial assets are Money, Gold, Coin and Currency.
- Custody/Amanat is a physical asset and must not be merged into the financial balance model.
- Kimia money unit is Rial; GoldPlatform display unit is Toman.
- Monetary and weight calculations must not use float.
- Ledger is intended to become the financial source of truth.
- Financial operations require idempotency, traceability and audit.
- Financial balances may be positive, zero or negative subject to approved policy.

## Existing implementation gaps

- `FinancialTransaction` and `LedgerEntry` are persistence skeletons, not a complete ledger engine.
- The financial transaction migration uses `$table->uuid()` without an explicit column name while the model expects `uuid`.
- Ledger entries identify an asset through a generic `currency` string, which cannot safely distinguish Money, Gold, Coin and Currency.
- Ledger posting, balancing validation, immutability, reversal, trace IDs and idempotency are not implemented.
- Wallet services mutate stored balances directly and are not yet ledger projections.
- Existing migrations must not be modified silently because deployed database state is unknown.

## Stage 02 first slice

Create contracts only for:

1. `FinancialAssetType`: Money, Gold, Coin, Currency
2. `MoneyUnit`: Rial, Toman
3. Exact decimal value handling without float and without an assumed global scale
4. Trace and idempotency identifiers
5. Unit tests for invariants

## Explicit stop conditions

No implementation will assume:

- Gold decimal scale
- Money decimal scale
- Coin quantity scale
- Currency quantity scale
- Negative balance limits
- Debit/credit sign convention
- Ledger account chart
- Posting timing relative to Kimia
- Rial/Toman conversion at persistence boundaries
- Tenant ownership schema

These require approved contracts or authoritative evidence before persistence migrations and posting services are created.
