# GoldPlatform V2 — Pricing / Quote / Order Authority Provenance

Status: CURRENT-PATH INVENTORY / HISTORICAL CLASSIFICATION
Scope: Pricing, Quote, Order entry authority, Customer Level enforcement
Date: 2026-08-08
Current work slice: PR #201 (`v2/identity-onboarding-policy-foundation`)

## Purpose

Prevent historical Pricing/Quote/Trading work from being silently treated as Canonical runtime authority.

This inventory follows:

PRESERVE -> INSPECT -> INVENTORY -> EXTRACT -> COMPARE -> VALIDATE -> CLASSIFY

No pricing formula, commission rule, spread, rounding rule, credit rule, customer limit, Kimia Write payload or financial balance authority is introduced by this document.

## Current canonical observation

The currently inspected customer order entry point is the legacy `POST /orders` path.

Current behavior:

- authenticated user id is derived server-side;
- client currently supplies price (`gold_price` / legacy unit-price equivalent);
- client may currently supply commission;
- `OrderService` uses those supplied financial values for total calculation/storage;
- this legacy route is not aligned with the newer canonical customer Tenant boundary (`role:customer`, `tenant.resolve`, `tenant.user-match`);
- effective Platform Customer Level / CustomerTradingPolicy is not enforced at this entry point.

Classification:

- Order lifecycle/model foundation: `REUSE AFTER FIX`;
- client financial price/commission authority: `REUSE AFTER FIX / FINANCIAL GATE`;
- Tenant/customer authorization boundary on legacy order entry: `REUSE AFTER FIX`;
- Customer Level policy enforcement at order entry: `NOT IMPLEMENTED`;
- financial rule activation: `BLOCKED BY GROUND TRUTH` until source/precedence is verified.

## Historical PR #109 — Trading validation foundation

PR #109 (`Stage 03: Trading validation foundation`) is:

- CLOSED;
- NOT MERGED;
- therefore not Canonical by itself.

Historical evidence from its patch includes:

- tenant-scoped `FinancialScope` boundaries;
- Quote and Order domain contracts;
- Quote lifecycle states (`requested`, `frozen`, `used`, `expired`, `cancelled`);
- creation of an Order from a previously frozen/used Quote;
- idempotency, concurrency and event/audit-oriented boundaries;
- explicit exclusion of Pricing formulas, Freeze duration, Customer limits, Wallet/Ledger mutation and Kimia Write mappings.

Classification:

`HISTORICAL ONLY — ARCHITECTURAL EVIDENCE / DUPLICATE CANDIDATE CHECK REQUIRED`

Important implication:

PR #109 proves that a Quote-first Order architecture was designed historically, but it does **not** establish a valid current price source, formula, commission rule or freeze duration.

No blind cherry-pick or direct revival is authorized.

## Historical PR #129 — Product & Pricing read foundation

PR #129 (`AP-13: add product and pricing read foundation`) is:

- CLOSED;
- NOT MERGED;
- therefore not Canonical by itself.

Historical evidence from its patch includes:

- read-only Product/ProductCategory/Pricing overview endpoints;
- stored Product `buy_price` / `sell_price` visibility;
- explicit refusal to infer price unit;
- explicit declaration that Formula management, Spread management and Rounding management were unsupported in that slice;
- no Kimia Sync/Write activation.

Classification:

`HISTORICAL ONLY — READ FOUNDATION EVIDENCE / DUPLICATE CANDIDATE CHECK REQUIRED`

Important implication:

Stored `products.buy_price` / `products.sell_price` columns are evidence of stored values only. They are **not** automatically the Canonical trading Quote authority and their unit/source/freshness must not be guessed.

## Historical PR #61 — dynamic asset/order foundation

PR #61 historically introduced/extended:

- dynamic AssetType: Money / Gold / Coin / Currency;
- dynamic Kimia Coin/Currency external IDs;
- exact Decimal order arithmetic;
- legacy OrderService support for caller-provided unit price / commission;
- Ledger-derived Wallet projection/reservation structures.

Current V2 authority boundary overrides any old source-of-truth wording from this historical line:

Kimia remains final authority for customer Money / Gold / Coin / Currency balances. Wallet/Ledger/Projection are not competing customer balance truth.

Classification:

- dynamic Asset identity foundation: `REUSE AFTER CURRENT-CODE VALIDATION`;
- client-provided price/commission behavior: `REUSE AFTER FIX`;
- Wallet/Ledger customer balance authority claims: `SUPERSEDED` by current V2 Kimia Source-of-Truth rule.

## Canonical balance-read authority already verified in V2-01

PR #199 established the current verified customer financial read chain:

`TenantContext -> authenticated user/Tenant match -> User.account_id -> Account -> Account.kimia_id -> BalanceReadRepository -> Kimia GET /api/voucher/balance/{id}`

Rules:

- no Wallet/Ledger/Projection fallback for customer Money/Gold/Coin/Currency;
- Tenant/account binding fails closed;
- Kimia Read failures do not produce fake zero balances.

Classification: `TESTED — NOT MERGED` on the V2-01 checkpoint branch.

This is the correct starting point for future eligibility checks, subject to the exact Customer Level rule being accepted.

## Platform Customer Level vs Kimia AccountGroup

Platform Customer Level/Group is a Tenant-owned policy concept.

Kimia AccountType/AccountGroup is Kimia accounting classification.

They are independent and must not be mapped as equivalents.

Existing Platform foundation:

- `User.group_id -> UserGroup`;
- `CustomerTradingPolicy -> UserGroup`;
- fields include available-balance requirement, negative-balance allowance, credit limit, locks and order/asset limits.

These fields are financially sensitive and are not runtime authorization merely because they exist in the schema.

Classification: `REUSE AFTER FINANCIAL GROUND TRUTH VALIDATION`.

## Required Canonical authority chain before Order activation

The target authority chain must be proven, not invented:

1. resolved Tenant and authenticated Customer;
2. current Customer Access Status permits the operation;
3. Platform Customer Level / effective trading policy is resolved for the same Tenant;
4. requested product/asset is valid and visible for that Tenant;
5. backend obtains an authoritative Quote/price snapshot from the accepted Pricing source;
6. commission/spread/rounding are calculated in Backend from accepted Tenant/Level/Product rules;
7. eligibility is evaluated using fresh Kimia financial balance when the selected Level requires balance/credit checks;
8. Order is created from the backend-authoritative Quote, not caller-supplied price/commission;
9. later Settlement/Kimia Write remains independently gated by actual Kimia Write Ground Truth, idempotency, readback and reconciliation.

## Unresolved Ground Truth gates

The following are not resolved by current canonical evidence alone:

- canonical external/base price provider per Tenant;
- exact pricing formula precedence;
- buy/sell spread semantics;
- rounding configuration and order of operations;
- commission formula and relationship to `UserGroup` fields;
- product-specific min/max versus group-level min/max precedence;
- credit-limit semantics and currency/unit;
- balance requirement semantics for each asset/side;
- Quote freeze duration and who may override it;
- whether existing `products.buy_price` / `sell_price` are inputs, outputs, cached snapshots or legacy values;
- final Quote persistence representation in the current canonical branch.

No implementation may guess these points.

## Safe next steps

1. search current canonical history for accepted Pricing/Quote ADRs and owner-confirmed formula examples;
2. inventory current `products`, pricing-related migrations/services/config and any price-provider integrations;
3. compare any surviving Quote code in the current branch against historical PR #109 before creating a duplicate;
4. identify exact accepted Commission/Level precedence from Project Ground Truth;
5. only then design the minimal backend Quote authority and tests;
6. keep `PUT /admin/customer-policies/{policy}` fail-closed until the corresponding financial rules are accepted.

## No-change statement

This checkpoint intentionally performs no runtime financial change, no Order route behavior change, no Customer Level activation, no Pricing calculation, no Commission calculation, no Kimia Write and no merge operation.
