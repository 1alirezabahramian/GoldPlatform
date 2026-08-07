# GoldPlatform V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification

**Status:** STARTED — CHARTER ONLY — IMPLEMENTATION NOT STARTED  
**Base branch:** `recovery/rc2-product-rebuild`  
**Base exact SHA:** `d9ee5fee69969fa02ac25c96d8e1653143ba413b`  
**Working branch:** `v2/v2-01-canonical-runtime-integration`

## Outcome

Establish a canonical, read-only, fail-closed runtime path that can prove how an authenticated GoldPlatform customer resolves to the correct Kimia account and how Money/Gold/Coin/Currency reads are reconciled against Kimia without creating a competing customer balance source.

V2-01 is not a rewrite of existing foundations. Existing canonical capabilities must be reused where valid.

## Why this is a distinct stage

Evidence review found that:

- the recovery/source reconstruction work is already closed from an evidence perspective;
- production-operations and CI-normalization work are separate integration/release concerns;
- `Idempotency`, `Audit`, `Outbox`, safe panel foundations, deny-by-default Kimia Write preparation, and workflow-only balance reservations already exist in repository history/canonical recovery and must not be rebuilt;
- PR #196 contains a narrowly scoped read-only reconciliation bridge, but its branch has materially diverged from canonical and must not be merged or cherry-picked blindly;
- the canonical exact SHA does not contain `CustomerAccountReconciliationService` or `KimiaInspectAccountReconciliation`;
- no equivalent dedicated canonical reconciliation capability was found in the reviewed PR/history searches.

Therefore Customer↔Kimia runtime binding verification is a real, non-duplicate integration outcome.

## Source-of-truth boundaries

1. **Kimia is final authority** for customer Money, Gold, Coin and Currency balances.
2. GoldPlatform must not create, infer, repair, or promote a competing financial balance.
3. GoldPlatform remains final authority for physical Custody/Amanat only.
4. Internal Ledger / Projection / Reservation structures are audit/workflow/reconciliation aids only.
5. Kimia Write remains disabled. `KIMIA_READ_ONLY=true` and `KIMIA_WRITE_ENABLED=false` remain safety defaults.

## Existing evidence to reuse

### PR #196 — evidence candidate, not merge source

Reusable concepts:

- read-only reconciliation classification;
- inspection of `accounts`, `external_accounts`, and `users.account_id`;
- conflict reporting such as duplicate user binding;
- proof that reconciliation inspection performs no mutation;
- fixed allow-listed Agent command for read-only inspection.

Classification: **REUSE AFTER FIX**.

Reason: the PR head is substantially diverged from canonical, so only the smallest evidenced capability may be reconstructed on canonical after revalidation.

## Explicitly forbidden in V2-01 unless separate Ground Truth/owner decision exists

- auto-link by mobile number, name, national code, account code, or similarity;
- creation/update/delete/backfill of customer-account bindings from reconciliation inspection;
- fallback to sample, zero, first-account, or hard-coded Kimia IDs;
- Kimia Write or guessed write payload/action/transaction code;
- independent Money/Gold/Coin/Currency balance mutation;
- silent Tenant/Connector/Permission redesign;
- broad cherry-pick/merge from PR #196;
- migration creation merely to make an assumed binding model fit.

## Required inspection before implementation

Preserve → Inspect → Inventory → Extract → Compare → Validate → Classify → Document → Integrate.

The implementation slice must first verify on canonical:

- current `Account` model/table and Kimia identifier semantics;
- current `ExternalAccount` model/table and provider/external identifier semantics;
- current `User`→Account relationship and cardinality;
- any Tenant/Company/Connector/Book scoping already present;
- existing authenticated customer account resolver, if any;
- existing Kimia read client/service boundaries;
- current API/OpenAPI customer asset read paths;
- exact runtime DB/migration evidence when an approved runtime environment is available.

## Target canonical resolution chain

The desired chain is evidence-driven and fail-closed:

`Authenticated Customer → approved platform binding → tenant/company/connector scope if present → exact Kimia AccountId → Kimia Read → reconciliation metadata`

No link in this chain may be invented. If a required link has no Ground Truth, the corresponding capability is `BLOCKED BY GROUND TRUTH` rather than guessed.

## Initial deliverables

1. Canonical binding inventory and conflict matrix.
2. Classification of PR #196 components: REUSE AS-IS / REUSE AFTER FIX / REFACTOR / SUPERSEDED.
3. Read-only canonical reconciliation service/command only if canonical schema supports it without guessed semantics.
4. Tests proving zero mutation and fail-closed behavior.
5. Traceability from customer binding source to Kimia read result.
6. Runtime evidence plan for real customer/account reconciliation.
7. Documentation of any Tenant/Connector/schema Ground Truth blocker.

## Exit criteria

V2-01 may only be declared complete when all applicable items below are evidenced:

- canonical resolver path is documented and implemented from verified schema/relationships;
- no hard-coded/sample/fallback Kimia account identifier exists;
- reconciliation inspection is read-only and tests prove no mutation;
- duplicate/orphan/ambiguous binding states are reported, not silently fixed;
- customer Money/Gold/Coin/Currency remain Kimia-authoritative;
- controllers do not call Kimia client directly;
- Kimia Write remains disabled;
- API/OpenAPI changes, if any, are synchronized;
- exact-head tests are `EXECUTED — PASS`, except independently documented upstream blockers that do not invalidate the tested capability;
- runtime reconciliation evidence is collected from an approved environment, or the missing environment/ground truth is explicitly classified as a blocking exit criterion;
- CI exact SHA, PR state and documentation are recorded;
- no conflicting canonical implementation is discovered during final duplicate scan.

## Current known external blocker

The repository currently has an upstream Composer security blocker involving `league/commonmark 2.8.3`, which can make Security Hardening / RC2 dependency audit fail independently of this stage. This blocker must not be suppressed or worked around by weakening security. It is tracked separately from V2-01 functional correctness.

## Current capability status

- V2-01 charter: **IMPLEMENTED — NOT TESTED**
- PR #196 direct integration: **REUSE AFTER FIX**
- Canonical Customer↔Kimia runtime binding: **NOT IMPLEMENTED / EVIDENCE INCOMPLETE**
- Kimia Write: **BLOCKED BY GROUND TRUTH**
