# GoldPlatform V2 — Quote Financial Asset Identity Decision

Status: STRUCTURAL DECISION — FINANCIAL QUOTE IDENTITY RESOLVED / NO RUNTIME ACTIVATION
Date: 2026-08-08
Work slice: PR #201 (`v2/identity-onboarding-policy-foundation`)

## Verified checkpoint

- Parent head: `95458f1d43b4dd4af53aaf9b5e4394f8b433505f`
- Operational Readiness #184: `EXECUTED — PASS`
- Backend RC1 Validation #568: `EXECUTED — PASS`
- PR #201: `OPEN — DRAFT — NOT MERGED — MERGEABLE`

## Purpose

Resolve the remaining structural identity blocker from `74_IDEMPOTENCY_CATALOG_QUOTE_SCHEMA_FIT_CHECK.md` without inventing a competing Product/Catalog authority and without activating Pricing, Trading, Settlement or Kimia Write.

## Canonical schema evidence

The exact V2 head contains no verified canonical local `Product` model/FK used by current Orders.

The canonical financial Order identity is already structural:

- `asset_type`
- `external_asset_id`
- `asset_quantity`
- `asset_unit`

The migration `2026_08_04_063000_add_asset_identity_to_wallet_accounts_and_orders.php` adds those fields directly to `orders` and does not bind Orders to a local Product table.

`AssetType` supports Money, Gold, Coin and Currency and requires an external asset identifier for Coin/Currency.

## Kimia catalog evidence

The current repository has explicit Kimia-derived cache/snapshot structures:

### `kimia_coins`

- local row id
- unique `kimia_id`
- name
- fineness
- weight
- type
- visibility
- `synced_at`

### `kimia_currencies`

- local row id
- unique `kimia_id`
- name
- visibility
- `synced_at`

These are Kimia-derived catalog/cache structures. They do not become an independent competing financial authority.

The runtime financial read path also reads Coin/Currency catalogs dynamically through `ProductReadRepository` from Kimia endpoints and uses Kimia identifiers for classification while keeping internal identifiers out of the customer-facing contract.

## Historical Product evidence classification

Historical PR #129 exposed `products` / `product_categories` from an older branch, but the PR is `CLOSED — NOT MERGED`. It remains `HISTORICAL ONLY` and is not promoted to canonical V2 Product authority.

The absence of a canonical current Product FK combined with the existing Order financial asset identity means creating a new local Product table solely to satisfy Quote persistence would be reinvention and a duplicate-authority risk.

## Decision

For **financial Quote identity** in V2, reuse the existing canonical financial asset identity boundary:

`asset_type + external_asset_id + explicit unit`

with these constraints:

1. Money/Gold/Coin/Currency remain Kimia-owned financial asset domains.
2. Coin/Currency external identity must be dynamically validated against grounded Kimia catalog identity; no sample ID may be hard-coded.
3. Money/Gold do not gain invented external IDs merely for schema symmetry.
4. A Quote must freeze enough display/provenance data to remain explainable even if a later Kimia catalog label changes, but the immutable snapshot must not become a competing live balance/catalog authority.
5. Customer-facing APIs must not expose internal Kimia identifiers merely because Quote persistence uses them internally.
6. Quote execution must validate current hard safety/access invariants separately from the immutable financial snapshot.
7. Physical Custody/Product identity is a separate domain and is not solved by this financial Quote decision.

## Classification

- Financial Quote asset identity: `REUSE AFTER FIX — STRUCTURAL TARGET RESOLVED`
- `AssetType + external_asset_id`: `REUSE` for financial Quote identity
- Kimia Coin/Currency caches: `REUSE AS KIMIA-DERIVED CACHE/SNAPSHOT`
- historical `products` / `product_categories`: `HISTORICAL ONLY`
- new generic local Product authority for financial Quote: `DUPLICATE CANDIDATE — DO NOT CREATE`
- physical Product/Custody catalog: `SEPARATE DOMAIN — NOT DECIDED BY THIS SLICE`

## Effect on Quote persistence blocker

The Product/Catalog identity blocker from document 74 is resolved **for financial Quotes**.

This does not authorize guessed financial values. Exact Decimal scale, provider mapping, x/y/z values, Customer Level adjustments, commission/tax/rounding values, limit precedence, credit semantics and Quote→Order/Kimia Write remain gated as previously documented.

A structural additive Quote migration may now be designed against the existing financial asset identity boundary, provided it remains non-executable/fail-closed for unresolved financial configuration and passes migration-fresh plus structural tests.

## Next safe step

Design the exact additive Quote table/index layout using `tenant_id`, authenticated customer identity, `asset_type`, nullable `external_asset_id`, explicit unit, immutable request/provenance/result snapshots, lifecycle timestamps/status and durable idempotency linkage/fingerprint. Before creating the migration, re-check current migration names and exact head to avoid duplicate creation.

No Merge is authorized by this decision.
