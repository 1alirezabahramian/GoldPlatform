# GoldPlatform — Wallet, Ledger & Balance Projection Deep Comparison

> Date: 2026-08-05
>
> Status: Evidence recorded — no product merge
>
> Compared refs: `main`, `feature/goldplatform-developer-mcp`, Stage 02 financial kernel

## 1. Accepted architecture boundary

Kimia is the final source of truth for customer Money, Gold, Coin and Currency balances.
GoldPlatform is the source of truth for physical Custody/Amanat.
Internal Journal, Ledger, Event, Idempotency and Projection components may exist only for audit, trace, workflow, intent/result, reconciliation and rebuildable operational projections.

## 2. Legacy Wallet model on main

`backend/app/Models/Wallet.php` stores:

- `rial_balance`
- `gold_balance`
- `blocked_rial`
- `blocked_gold`

Classification: `ARCHITECTURE DRIFT — HISTORICAL ONLY`.

Reason: these columns model independent final customer balances for Money and Gold. They must not be used by new customer reads, inventory checks, order validation or settlement authorization.

## 3. Legacy WalletAccount on main

`backend/app/Models/WalletAccount.php` stores `balance` and `blocked_balance` and calculates `available_balance` locally.

Classification: `ARCHITECTURE DRIFT — DO NOT USE AS CUSTOMER BALANCE`.

The model may remain temporarily for forensic compatibility, but it is not a canonical source for financial balance.

## 4. Legacy LedgerService on main

`backend/app/Services/LedgerService.php` creates debit/credit entries linked directly to `wallet_account_id`.

Positive properties:

- decimal values are passed as strings;
- entries are traceable through `FinancialTransaction`;
- transfer writes both debit and credit entries in one database transaction.

Conflicts:

- the accounting identity is coupled to legacy wallet accounts;
- no tenant boundary is visible in this service;
- it does not prove that its entries are only operational/audit records;
- consumers may incorrectly interpret it as the source of customer balance.

Classification: `SUPERSEDED IMPLEMENTATION / HISTORICAL DONOR ONLY`.

## 5. Historical product LedgerService

The historical product version adds validation, decimal normalization, balance checks and per-asset double-entry assertions.

These are useful implementation ideas, but the service remains coupled to `wallet_account_id`.

Classification: `DONOR FOR VALIDATION LOGIC — NOT CANONICAL PERSISTENCE`.

Its validation and balancing concepts may be reused only inside the Stage 02 financial kernel contracts, not by restoring legacy wallet balance ownership.

## 6. Historical BalanceProjectionService

`backend/app/Services/Wallet/BalanceProjectionService.php` derives total and blocked values from ledger entries and reservations, then writes them into `WalletAccount.balance` and `WalletAccount.blocked_balance` through `rebuild()`.

This is a rebuildable projection technically, but in the historical product it is exposed through `WalletAccount::getAvailableBalanceAttribute()` as an application balance.

Classification:

- projection calculation concept: `DONOR — OPERATIONAL USE ONLY`;
- persistence into legacy wallet account balance: `ARCHITECTURE DRIFT`;
- use as customer final financial balance: `PROHIBITED`.

## 7. Stage 02 financial kernel on main

Stage 02 contains tenant-scoped Journal, Event Store, Idempotency, Reservation and Balance Projection contracts and database adapters.

Positive properties:

- exact decimal value objects;
- tenant/company/branch scope support;
- journal lifecycle and reversal;
- idempotency and correlation identifiers;
- concurrency boundaries;
- balanced double-entry invariants;
- separate Custody boundary;
- persistence adapters and tests.

The Stage 02 completion audit explicitly records that business-specific debit/credit direction, projection effects, credit-limit enforcement, negative-balance authorization, chart of accounts, Kimia write payloads and legacy reconciliation remain open.

Classification: `CANONICAL OPERATIONAL KERNEL CANDIDATE — SEMANTIC RESTRICTION REQUIRED`.

Required restriction:

> `financial_balance_projections` are internal workflow/reconciliation snapshots only. They are not the final customer balance for Money, Gold, Coin or Currency. Customer-facing and authorization reads must resolve to Kimia, with timestamped cache/snapshot metadata when used for performance.

## 8. Canonical decision

### Keep and verify

- Stage 02 exact decimal value objects
- tenant-scoped journal contracts
- event store
- idempotency registry
- atomic posting boundaries
- concurrency guards
- reservation lifecycle, only as workflow state
- projection interfaces, only after explicit non-authoritative naming and documentation

### Do not integrate as canonical balance

- `Wallet.rial_balance`
- `Wallet.gold_balance`
- `Wallet.blocked_rial`
- `Wallet.blocked_gold`
- `WalletAccount.balance`
- `WalletAccount.blocked_balance`
- `WalletAccount.available_balance`
- historical `BalanceProjectionService::rebuild()` for customer financial balances
- order or settlement checks based on legacy wallet values

### Preserve for evidence

Legacy migrations, models and services must not be deleted during Recovery. They require a later compatibility and data-migration plan.

## 9. Required reconstruction tests

- architecture test: no customer financial response reads legacy wallet balances;
- architecture test: no order inventory validation reads legacy wallet balances;
- architecture test: no registration flow creates Money/Gold balance accounts;
- architecture test: Custody remains outside financial projections;
- tenant-isolation tests for Stage 02 adapters;
- negative Kimia balance presentation test;
- projection rebuild/reconciliation test with explicit `source=kimia` and synchronization timestamp;
- migration compatibility test before any legacy table retirement;
- MySQL and Redis concurrency execution.

## 10. Current conclusion

The project contains two financial mechanisms:

1. legacy Wallet/WalletAccount balances — non-canonical and architecture drift;
2. Stage 02 operational financial kernel — technically stronger and reusable only for audit, workflow, idempotency, eventing and reconciliation.

No code should restore GoldPlatform as the final financial balance owner.

Status: `WALLET BALANCE PATH SUPERSEDED — STAGE 02 KERNEL KEEP WITH NON-AUTHORITATIVE PROJECTION BOUNDARY`.
