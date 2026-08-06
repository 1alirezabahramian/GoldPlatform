# GoldPlatform V2 — Architecture Contract

**Status:** ACCEPTED BASELINE — subject to evidence-based refinement

## Core principle

`Complex Backend — Simple Frontend`

Financial complexity, Kimia integration, permissions, audit, idempotency, settlement and reconciliation belong in Backend. Frontend is Persian, RTL, mobile-first, white-label and free of independent financial logic.

## Authority boundaries

| Domain | Final authority |
|---|---|
| Money / Gold / Coin / Currency balances | Kimia |
| Physical Custody / Amanat | GoldPlatform |
| Order lifecycle and workflow intent | GoldPlatform |
| Audit, trace, idempotency and reconciliation evidence | GoldPlatform |
| Final result of a Kimia financial write | Kimia response plus post-write readback |

Internal Wallet, Ledger or Projection records must never compete with Kimia as customer balance authority.

## Required layers

1. HTTP Controller / Route boundary
2. Application service
3. Domain policy and workflow
4. Integration repository or approved gateway
5. Kimia read client or separately approved write client
6. Audit, idempotency, outbox and reconciliation evidence

Forbidden shortcuts:

- Controller to Kimia Client
- Frontend to Kimia
- Frontend financial calculations
- raw Eloquent model serialization for sensitive APIs
- client-selected Tenant or financial authority
- hidden automatic write/retry/replay

## Read/write separation

Kimia Read and Kimia Write must use separate contracts, policies and retry behavior. Write remains disabled until real ground truth, owner approval, idempotency, readback and reconciliation are complete.

## Financial precision

- Decimal or decimal string only
- no float for money, weight, price, quantity or commission
- Rial/Toman conversion is centralized in Backend
- unavailable data is not zero
- negative balances remain valid values

## Dynamic assets

Coin and Currency catalogs are loaded from Kimia. Sample IDs may appear only in fixtures explicitly marked historical or synthetic; they must not become production configuration.

## Customer API boundary

Customer-facing APIs must:

- be versioned;
- require authentication except explicitly accepted public Bootstrap/Auth allowlists;
- enforce ownership and Tenant safety;
- use explicit resources/allowlists;
- prevent sensitive caching;
- provide request trace identifiers;
- hide Kimia, Ledger, Voucher, internal IDs and accounting vocabulary;
- preserve decimal values as strings;
- fail closed when Kimia-backed data is unavailable.

## Custody boundary

Custody represents physical items and must remain separate from Money/Gold/Coin/Currency balances. Custody creation, conversion, resale and delivery require explicit lifecycle, ownership, branch/inventory evidence, permission, audit and idempotency.

## Order and settlement boundary

An Order expresses customer intent and lifecycle. Settlement cannot be marked complete from Ledger balance alone. Financial completion requires verified Kimia result evidence and post-write balance readback. Internal reservations are workflow intent, not proof of sufficient final balance.

## Outbox and automation

Sensitive replay and automatic dispatch are disabled by default. Schedulers and queued jobs must not execute Kimia or settlement operations without an explicit deployment gate, approved handler, idempotency and audit contract.

## Tenant and white-label safety

White-label is mandatory. Tenant, Company and Branch scoping must not be invented from labels or UI. Until accepted architecture and migrations prove scope, related capabilities remain `BLOCKED BY GROUND TRUTH` or read-only discovery.

## Frontend contract

Three independent experiences are required: Customer, Operator and Admin. They may share design tokens and components, but not permissions or workflows. Navigation is not authorization. Backend permissions remain authoritative.

Required states: loading, empty, error, forbidden, unavailable and offline where safe. Offline storage must never cache API financial data or enable financial actions.

## Delivery rule

No capability is Complete without traceability from Requirement through source, architecture, database, Backend, API, OpenAPI, Frontend, Permission, Audit, Idempotency, tests, exact CI SHA, PR, merge and visual verification.
