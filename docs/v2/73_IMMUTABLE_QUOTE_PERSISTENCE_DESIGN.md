# GoldPlatform V2 — Immutable Quote Persistence Design

Status: STRUCTURAL DESIGN — NO MIGRATION / NO RUNTIME ACTIVATION
Date: 2026-08-08
Work slice: PR #201 (`v2/identity-onboarding-policy-foundation`)

## Goal
Translate the accepted immutable Quote boundary into an additive persistence design without guessing unresolved financial policy values and without activating Pricing, Order execution, balance mutation or Kimia Write.

## Preconditions confirmed
- Current V2 branch has no canonical Quote persistence matching the accepted immutable financial snapshot contract.
- Historical Trading/Quote lifecycle is evidence only (`REUSE AFTER FIX`).
- Historical Trading Quote persistence is insufficient (`REBUILD`).
- Existing Order persistence remains legacy/current runtime evidence and is not silently rewritten in this slice.
- Money/Gold/Coin/Currency balance authority remains Kimia.

## Design principle: snapshot, do not re-derive
An issued Quote must remain explainable after later changes to Tenant settings, Product configuration, Customer Level, price-provider mapping, commission/tax/rounding policy or customer access state. Financially relevant effective inputs/results must therefore be frozen with the Quote rather than reconstructed from mutable current configuration.

## Proposed aggregate boundaries
### Quote identity/lifecycle record
Conceptual responsibilities: public Quote identifier; tenant/customer/product scope; buy/sell side; input mode; lifecycle status; issued/expires/confirmed/used/cancelled/expired timestamps as applicable; idempotency request identity/fingerprint; immutable snapshot version reference.

### Quote financial/provenance snapshot
Conceptual responsibilities: normalized customer intent; price observation provenance; effective pricing/formula policy; effective Customer Level/side adjustment; commission/tax/rounding policy and results where applicable; normalized quantity/weight; authoritative unit price/subtotal/final total; units/currency; policy/source version/snapshot evidence; authorized audited override evidence where applicable.

One table versus immutable child records remains an implementation choice after migration/index/query validation.

## Required constraints
Implementation must enforce/test: one Tenant; one authenticated Customer in that Tenant; no cross-Tenant product/catalog scope; no cross-Tenant/customer idempotency replay; no reactivation of expired/cancelled/used Quote; no silent financial snapshot edit; Tenant ID never client authority; no hard-coded Coin/Currency sample IDs.

## Decimal storage
No float/double for money, weight, price, quantity, fee, tax, rounding or formula values. Use exact decimal-capable storage or exact decimal strings with explicit validation. Exact scale/precision remains a product/policy gate.

## Idempotency
Existing Idempotency Registry must be evaluated before duplicate Quote-specific infrastructure. Same key + same normalized request returns same safe Quote; same key + materially different request conflicts; scope is Tenant/customer bound.

Classification: existing Idempotency foundation = `DUPLICATE CANDIDATE / REUSE PREFERRED AFTER FIT CHECK`.

## Quote -> Order boundary
Order creation/execution must consume a valid Backend Quote rather than client-authoritative price/commission/total. Order must retain a durable reference to the Quote/snapshot used so Audit/Reconciliation can explain intent/result. Legacy Order schema/service is not changed in this design slice.

## Access revalidation
Quote issuance freezes financial policy/results, but execution still fails closed on current non-overridable safety/access gates: Suspended/Blocked customer, inactive Tenant, expired/used/cancelled Quote, or another hard invariant. Later Customer Level/config changes do not silently mutate an issued Quote; whether they invalidate it requires explicit policy.

## Kimia boundary
Quote creation may use Kimia Read for grounded balance eligibility. Kimia Write is prohibited/not applicable. Quote execution/settlement Kimia Write remains separately gated by real Ground Truth.

## Migration strategy
Immediately before implementation: re-search migration history; create only additive migrations; never rewrite applied migrations; do not destructively convert legacy Order fields in this slice; validate indexes/uniqueness for public ID, Tenant/customer scope, status/expiry and idempotency; require migration-fresh CI pass.

## Structural test plan
Tenant isolation; Customer isolation; immutable snapshot behavior; Backend-clock expiry; illegal lifecycle transitions; idempotent replay; changed-request conflict; cross-Tenant idempotency isolation; exact decimal preservation; no client Tenant authority; no client price/commission/tax/rounding authority; no Kimia Write from Quote creation.

Financial expected-value tests wait until each tested rule/value is grounded.

## Deferred decisions
One-table vs child-snapshot layout; exact Decimal scale; exact lifecycle enum names; exact Product/Catalog FK target; provider observation schema; commission/tax/rounding granularity; whether Customer Level changes invalidate outstanding Quotes; Quote-to-Order execution transaction; Kimia Write/settlement payload.

## Classification
- Persistence architecture: `DESIGNED — NOT IMPLEMENTED`.
- New migration: `NOT IMPLEMENTED`.
- Tests: `NOT IMPLEMENTED`.
- Runtime Pricing/Quote: `NOT IMPLEMENTED`.
- Historical lifecycle: `REUSE AFTER FIX`.
- Existing Idempotency Registry: `DUPLICATE CANDIDATE — FIT CHECK REQUIRED`.
- Legacy Order financial authority: `REUSE AFTER FIX`.

## Next safe step
Inventory existing Idempotency Registry and Catalog/Product models/migrations against this design. Resolve reusable identity/scope primitives before creating any Quote migration.