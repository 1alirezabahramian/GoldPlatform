# GoldPlatform V2 — Pricing Ground Truth Reconciliation

Status: ACCEPTED-GROUND-TRUTH RECONCILIATION / NO RUNTIME ACTIVATION
Date: 2026-08-08
Current work slice: PR #201 (`v2/identity-onboarding-policy-foundation`)

## Why this document exists

Current Order/Pricing inventory initially identified missing pricing authority in the active runtime. A deeper Project Memory / Domain Workshop check found that important pricing decisions had already been accepted historically under CP-003..CP-005 / ADR-029.

The current PR branch does not contain `docs/ADR/ADR-029-catalog-pricing-foundation-contract.md`, so those decisions must not be silently treated as if the current branch already implements them.

This document preserves the accepted business/architecture decisions while explicitly reconciling them with newer V2 authority rules.

No formula, commission, rounding, Quote, Order, Customer Level or Kimia Write runtime is activated by this checkpoint.

## Accepted Pricing Pipeline

Accepted Project Memory defines the Pricing/Quote order of operations as:

1. resolve Tenant/product/customer policy and hard gates;
2. obtain a fresh price observation;
3. normalize explicit units, including backend-only Rial/Toman conversion where applicable;
4. apply the product-specific price formula;
5. apply side/customer-group adjustment;
6. resolve quantity/subtotal;
7. apply commission/tax using an explicit declared base;
8. apply one final configured rounding step;
9. persist an immutable backend Quote snapshot.

This order is materially stronger than the legacy `OrderService` path that accepts client price/commission.

Classification: `OWNER/ADR ACCEPTED — RUNTIME NOT YET ACTIVATED ON CURRENT BRANCH`.

## Accepted Formula Shape

For a configured product/API price mapping:

`BasePrice = (ApiPrice / x) * y + z`

Rules:

- x/y/z are configuration/policy, not Frontend input;
- calculations use exact Decimal/String Decimal, never float;
- input and output units must be explicit;
- the formula is product/connector scoped and must not be globally assumed for every asset.

Domain Workshop also records backend-only Rial/Toman conversion and a conceptual mapping example of `x=10, y=1, z=0` for Rial->Toman.

Important: unit/scale semantics must be explicit in the actual policy. No formula is executable merely because the generic shape is known.

## Accepted Customer Group / Side Adjustments

For each Platform Customer Level/Group and product, pricing may have separate adjustments for:

- platform sells to customer (customer buys);
- platform buys from customer (customer sells).

Adjustment shape may be:

- fixed amount;
- percentage;
- increase or decrease according to configured policy.

This is GoldPlatform Tenant commercial policy and is independent of Kimia AccountType/AccountGroup.

Classification: `OWNER-CONFIRMED / ACCEPTED POLICY SHAPE — VALUES REQUIRE TENANT/PRODUCT CONFIG`.

## Commission inputs already preserved

Project Memory preserves these owner-confirmed inputs:

- customer buy commission = 0%;
- customer sell commission = 1% using the 18K API base;
- customer-group buy/sell levers exist.

These values are **not automatically executable for every Tenant/product/level**. The accepted memory itself requires missing product scope, commission base, precedence, tax, precision and other semantics to fail closed.

Current V2 clarification also confirms that Customer Level/Group is the platform mechanism by which capabilities, balance requirements, limits and commercial parameters may differ between normal/special/VIP-like customers.

Therefore no current runtime should hard-code 0%/1% globally.

## Accepted Rounding contract

Accepted decisions require one final configured rounding step after policy/formula/commission stages.

Domain Workshop contains an example of final-digit rounding with `rounding_digits = 4` / nearest 10,000, but this example must not be promoted to a universal default without Tenant/product policy.

Classification: `ACCEPTED CONFIGURABLE CAPABILITY — DEFAULT/INCREMENT REQUIRE CONFIG`.

## Accepted Quote contract

Project Memory defines a proposed immutable Customer Quote boundary:

`POST /api/v1/customer/quotes`

Key accepted properties:

- Client must not send price, fee, tax, rounding or formula values;
- Tenant and authenticated customer are derived/verified in Backend;
- 18K order input may be by exact weight or exact Toman amount when policy allows;
- Quote contains backend-authoritative unit price, subtotal, applicable commission/tax lines and total;
- Quote records observed/issued/expires/server UTC timestamps;
- `requires_confirmation = true`;
- Frontend never recalculates financial values;
- immutable internal policy/source/formula/audit snapshot is retained server-side;
- edit or expiry creates a new Quote and requires customer reconfirmation;
- same idempotency request + same payload returns the same safe Quote; same idempotency key + different payload conflicts.

The historical contract preparation explicitly stated that Route/Controller/Service/DB runtime had **not** yet been activated at that checkpoint.

Classification: `ACCEPTED CONTRACT / CURRENT RUNTIME IMPLEMENTATION MUST BE REVALIDATED`.

## Accepted Quote freeze

Accepted Project Memory records policy-selected freeze choices of:

`3 / 5 / 6 minutes`

with Backend UTC as authoritative clock.

Rules:

- browser countdown is presentation only;
- expiry is enforced in Backend;
- expired/edited Quote is not silently mutated or extended;
- new Quote + customer reconfirmation is required.

A Tenant/customer/product policy must determine eligibility/default. Do not infer one universal default.

## Accepted policy precedence

Accepted CP-005 precedence is conceptually:

1. non-overridable hard safety/invariants;
2. Tenant baseline;
3. Catalog asset/product policy;
4. Platform Customer Level/Group policy;
5. authorized, time-bounded, audited administrative override.

Hard safety denies dominate commercial allows.

This is directly compatible with the current V2 separation between Customer Access Status and Platform Customer Level.

## Current V2 authority supersession

An older Project Memory authority matrix states that GoldPlatform Ledger was financial balance authority with Kimia used for posting/reconciliation.

That statement is **SUPERSEDED** by the current V2 rule:

- Kimia is final authority for customer Money / Gold / Coin / Currency balances;
- GoldPlatform Ledger/Journal/Projection are audit/trace/intent/result/settlement/reconciliation structures only;
- customer eligibility that requires current financial balance must use the verified Kimia read path and must not fall back to Wallet/Ledger balance.

No older pricing/catalog decision may revive a competing customer balance authority.

## Historical PR classification

### PR #109

`CLOSED — NOT MERGED`.

Useful as historical architectural evidence for Tenant-scoped Quote/Order lifecycle, idempotency and validation. It explicitly excluded Pricing formulas, freeze duration and customer limits.

Classification: `HISTORICAL ONLY / DUPLICATE CANDIDATE CHECK`.

### PR #129

`CLOSED — NOT MERGED`.

Useful as historical read-only Product/Pricing evidence. It explicitly declared formula/spread/rounding unsupported in that slice and did not establish Quote authority.

Classification: `HISTORICAL ONLY / DUPLICATE CANDIDATE CHECK`.

### PR #61

Historical dynamic asset/order foundation. Useful for dynamic Coin/Currency identities and exact Decimal mechanics, but its Wallet/Ledger balance authority wording is superseded by current V2 Kimia Source-of-Truth.

## What is now resolved

The following are no longer correctly described as wholly unknown:

- high-level Price Engine order of operations;
- generic `(api/x)*y+z` formula shape;
- Customer Group side-adjustment capability;
- configurable final rounding capability;
- immutable Backend Quote requirement;
- 3/5/6 minute policy-selected freeze choices;
- hard-safety > Tenant > Product > Customer Level > audited override precedence;
- Frontend prohibition from supplying/recalculating authoritative financial Quote values.

## What remains blocked before runtime activation

Still requires current-branch inventory/configuration/grounding:

- which Tenant price provider/connector supplies each product observation;
- provider field/key mapping and freshness threshold;
- product-specific x/y/z unit/scale values;
- exact rounding increment/mode/default per product/Tenant;
- exact commission base/scope/precedence per product/level;
- tax applicability/base;
- exact min/max order precedence between Product and Customer Level;
- exact credit/balance eligibility semantics per Level and side;
- current canonical persistence/schema for immutable price observation and Quote;
- current implementation status of Catalog mapping tables/contracts referred to by old ADR-027..029 memory;
- confirmation that no newer GitHub-backed accepted ADR superseded these pricing decisions.

## Immediate safe implementation direction

Before creating new Pricing/Quote services:

1. inventory current branch for surviving Catalog/Quote/Price schema and services;
2. locate GitHub history/commit that contained ADR-027..029 and compare against current V2 base;
3. classify any current equivalent as `DUPLICATE CANDIDATE` before adding code;
4. repair legacy `POST /orders` authority only after the backend Quote path is grounded;
5. use current verified Kimia Balance Read for eligibility where policy requires financial balance;
6. keep CustomerTradingPolicy mutations fail-closed until exact policy field semantics are reconciled with this accepted Pricing contract.

## No-change statement

No runtime financial behavior, Pricing calculation, Quote API, Order calculation, Customer Level effect, Kimia Write, migration, merge or production setting is changed by this document.
