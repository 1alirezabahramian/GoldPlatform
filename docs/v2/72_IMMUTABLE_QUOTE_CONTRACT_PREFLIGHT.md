# GoldPlatform V2 — Immutable Quote Contract Preflight

Status: CONTRACT PREFLIGHT — NO RUNTIME ACTIVATION
Date: 2026-08-08
Work slice: PR #201 (`v2/identity-onboarding-policy-foundation`)

## Purpose

Define the minimum V2 immutable Quote boundary that is already supported by accepted Project Ground Truth, while explicitly separating unresolved financial configuration from implementable structural requirements.

This is not permission to activate Pricing, Trading, Kimia Write, tax, commission defaults, or Customer Level financial behavior.

## Evidence / provenance

Current V2 reconciliation:
- `docs/v2/70_PRICING_GROUND_TRUTH_RECONCILIATION.md`
- `docs/v2/71_TRADING_QUOTE_RECOVERY_CLASSIFICATION.md`

Historical Trading/Quote code is useful only for lifecycle/idempotency/scope patterns and is not sufficient as the V2 financial Quote persistence contract.

Repository search on the current branch found no surviving canonical `Quote` / `quote_snapshot` / `price_observation` persistence implementation matching the accepted contract. First-search absence is not treated as proof of global historical absence; historical candidates remain evidence only unless GitHub chronology makes them canonical.

## Non-negotiable authority boundary

The Backend is the only authority that may construct an executable financial Quote.

The client MUST NOT supply authoritative:
- unit price;
- commission/fee amount;
- tax amount;
- rounding result;
- product formula coefficients;
- price-source observation value;
- final total.

Frontend may submit only customer intent and identifiers allowed by the API contract. It displays Backend-returned financial values without recalculation.

## Minimum immutable Quote identity and scope

A V2 Quote must be bound to immutable server-resolved scope sufficient to prevent replay/cross-scope use:

- Quote public identifier;
- resolved Tenant identity;
- authenticated Customer identity;
- product/asset identity;
- side: customer buys or customer sells;
- idempotency identity/fingerprint;
- issued timestamp in Backend UTC;
- expiration timestamp in Backend UTC;
- lifecycle status;
- customer-confirmation requirement/status or equivalent confirmation evidence.

Tenant must come from verified Host -> TenantContext, never a client-selectable Tenant authority.

Exact database column names/types remain an implementation decision after schema inventory.

## Customer intent snapshot

The Quote must preserve the exact customer intent used to calculate it.

For 18K gold, accepted Ground Truth allows request intent by exact weight or exact Toman amount when policy permits. Therefore the snapshot must be able to distinguish input mode and preserve the normalized Backend interpretation.

For Coin/Currency/other dynamic products, product identity must use the canonical catalog/Kimia mapping rather than hard-coded sample IDs.

Exact supported input modes for every product remain product-policy configuration and must not be inferred globally.

## Price observation provenance

The Quote must retain enough immutable provenance to explain which fresh market observation produced the price, including conceptually:

- configured price provider/connector identity;
- provider field/key or mapped observation identity;
- observed value as exact Decimal/String Decimal;
- observed timestamp;
- freshness evaluation at Quote issuance;
- explicit source/input unit.

Unresolved before executable runtime:
- provider per Tenant/product;
- provider key/field mapping;
- freshness threshold;
- connector-specific failure semantics.

These are configuration/Ground Truth gates, not Frontend inputs.

## Pricing policy snapshot

The Quote must retain the effective policy inputs needed to reproduce/explain the result, conceptually including:

- product formula configuration (`x`, `y`, `z`) where applicable;
- explicit unit-normalization/conversion rule;
- effective Platform Customer Level/Group adjustment for the side;
- effective commission rule and declared base;
- effective tax rule and declared base, if applicable;
- effective rounding rule;
- effective min/max/eligibility policy identifiers or version/snapshot evidence;
- any authorized audited override that affected the Quote.

The effective values must be server-derived and frozen with the Quote. Later policy edits must not silently mutate an already issued Quote.

## Financial result snapshot

The immutable Quote must preserve Backend-authoritative exact Decimal/String Decimal results sufficient for customer display and audit, conceptually:

- normalized quantity/weight and unit where applicable;
- authoritative unit price;
- subtotal;
- commission/fee line(s) when applicable;
- tax line(s) when applicable;
- rounding adjustment/result where applicable;
- final total;
- final currency/unit.

No float is permitted for financial values.

Exact scale/precision is not guessed by this preflight.

## Freeze / expiry

Accepted policy supports freeze choices of 3 / 5 / 6 minutes, selected by applicable policy.

Rules:
- Backend UTC is authoritative;
- browser countdown is presentation only;
- expired Quote cannot be executed;
- edit/reprice/expiry produces a new Quote rather than mutating/extending the old financial snapshot;
- customer reconfirmation is required for the new Quote.

This preflight does not choose a universal default freeze duration.

## Idempotency

For Quote creation:
- same idempotency key + same normalized request intent/scope -> same safe Quote result;
- same idempotency key + materially different request -> conflict;
- idempotency must be Tenant/customer scoped;
- sensitive internal policy/source details need not all be exposed in the replay response, but server-side audit/provenance must remain intact.

## Balance / eligibility boundary

Where Customer Level/Product policy requires sufficient Money/Gold/Coin/Currency balance, eligibility must use the verified Kimia Read path.

GoldPlatform Wallet/Ledger/Projection MUST NOT be used as a fallback final customer balance authority.

Negative Kimia balances remain representable; whether a particular Level may trade without sufficient balance is a Platform Customer Level policy decision and must be evaluated explicitly.

No Kimia Write is part of Quote creation.

## Customer Access Status boundary

A financially valid Quote does not override Customer Access Status.

Limited / Suspended / Blocked customers must fail closed for operations prohibited by their current access policy even if an older Quote exists.

Onboarding approval, Access Status and Customer Level remain separate dimensions.

## Known unresolved financial gates

The following are deliberately NOT invented here:

1. exact Tenant/product price provider and observation key;
2. freshness threshold;
3. product-specific formula x/y/z values and unit semantics;
4. exact Customer Level adjustment values;
5. commission base/scope/precedence per product/side/level;
6. tax applicability and base;
7. exact rounding increment/mode/default;
8. min/max precedence between product and Customer Level;
9. credit/insufficient-balance semantics per Level and side;
10. exact decimal scale per field/product;
11. persistence field names/types/indexes;
12. Quote-to-Order execution/settlement/Kimia Write payload.

Runtime financial activation that depends on any unresolved item remains fail-closed.

## Persistence design constraints for the next slice

A future migration/schema must:
- be additive; never rewrite an applied historical migration;
- preserve immutable Quote financial/provenance snapshots;
- be Tenant scoped;
- support idempotency conflict detection;
- support expiration and single-use/execution lifecycle;
- avoid client-authoritative financial fields;
- avoid hard-coded Coin/Currency IDs;
- keep Custody separate from financial balance;
- remain auditable and reconciliation-friendly.

Before creating it, migration history and any surviving Quote/Pricing tables must be searched again to avoid duplicates.

## Classification

- Backend-authoritative immutable Quote boundary: `OWNER/ADR ACCEPTED — READY FOR STRUCTURAL DESIGN`.
- Historical Quote lifecycle patterns: `REUSE AFTER FIX`.
- Historical Quote persistence: `REBUILD`.
- Current canonical V2 Quote runtime: `NOT IMPLEMENTED` based on current-branch inventory.
- Pricing financial values/defaults: `BLOCKED BY CONFIG/GROUND TRUTH` where listed above.
- Kimia Write during Quote creation: `NOT APPLICABLE / PROHIBITED`.

## Next safe step

1. re-inventory migrations/models/routes for duplicate Quote/Pricing persistence candidates;
2. design an additive V2 Quote persistence schema using only structural fields whose semantics are grounded;
3. keep unresolved financial policy values as explicit configuration/gates rather than guessed defaults;
4. write lifecycle/immutability/idempotency/tenant-isolation tests before enabling executable pricing.