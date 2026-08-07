# GoldPlatform V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification

**Status:** IN PROGRESS — RECONCILIATION TESTED — RESOLVER BLOCKED  
**Base branch:** `recovery/rc2-product-rebuild`  
**Base exact SHA:** `d9ee5fee69969fa02ac25c96d8e1653143ba413b`  
**Working branch:** `v2/v2-01-canonical-runtime-integration`

## Outcome

Establish a canonical, read-only, fail-closed runtime path that can prove how an authenticated GoldPlatform customer resolves to the correct Kimia account and how Money/Gold/Coin/Currency reads are reconciled against Kimia without creating a competing customer balance source.

V2-01 is not a rewrite of existing foundations. Existing canonical capabilities must be reused where valid.

## Current result

Canonical source proves a structural `User.account_id -> Account.kimia_id` path and contains canonical Kimia read repositories. It does not contain an approved runtime workflow that populates `users.account_id`.

A narrowly reconstructed read-only reconciliation capability is present and tested. It reports conflicts and performs no repair. Customer financial endpoints remain intentionally fail-closed.

White-label is a confirmed product requirement, but the exact Tenant/Company/Connector/Book runtime model is not yet grounded by a currently recovered authoritative source.

## Source-integrity correction

Earlier V2-01 notes referred to `ADR-024` / `ADR-026`, detailed binding cardinality/immutability rules, and a specific Tenant/connector model as accepted Ground Truth. During the current evidence pass those exact sources could not be re-established from:

- current GitHub PR/commit/code/branch searches;
- available `00_PROJECT_MEMORY.md`;
- `41_GOLDPLATFORM_DOMAIN_WORKSHOP_2026-07-28.md`;
- `08_KIMIA_INTEGRATION_AUDIT.md`;
- current continuation/handoff sources.

Under NO GUESSING those details are now **BLOCKED BY SOURCE RECOVERY** and are not allowed to drive a migration, guard or resolver until the authoritative source is recovered or a genuine owner decision is required.

This does not claim those ADRs never existed. A first-search miss is not absence proof.

## Source-of-truth boundaries

1. **Kimia is final authority** for customer Money, Gold, Coin and Currency balances.
2. GoldPlatform must not create, infer, repair, or promote a competing financial balance.
3. GoldPlatform remains final authority for physical Custody/Amanat only.
4. Internal Ledger / Projection / Reservation structures are audit/workflow/reconciliation aids only.
5. Kimia Write remains disabled/deny-by-default.
6. White-label is required, but its storage/routing implementation must be evidence-driven rather than invented.

## Existing evidence reused

### Canonical Kimia Read

Merged PR #150 supplies the isolated Kimia read client and Account/Balance/Product repositories. Classification: **REUSE AS-IS**.

### Customer fail-closed financial state

Current Customer Dashboard/Assets controllers return `KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED` until a verified resolver exists. Classification: **REUSE AS-IS**.

### PR #196 — evidence candidate, not merge source

Reusable concepts:

- SELECT-only reconciliation classification;
- inspection of `accounts`, `external_accounts`, and `users.account_id`;
- duplicate/orphan conflict reporting;
- zero-mutation proof.

Classification: **REUSE AFTER FIX**.

No broad merge or cherry-pick from PR #196 is permitted.

## Explicitly forbidden in V2-01 unless separate Ground Truth/owner decision exists

- auto-link by mobile, name, national code, account code, similarity, first record, zero or sample ID;
- creation/update/delete/backfill of customer-account bindings from reconciliation inspection;
- Kimia Write or guessed write payload/action/transaction code;
- independent Money/Gold/Coin/Currency balance mutation;
- invented Tenant/Company/Connector/Book schema or routing;
- broad cherry-pick/merge from PR #196;
- migration creation merely to force an assumed binding model.

## Canonical findings

- `accounts.kimia_id` is non-null and unique in canonical migration.
- `users.account_id` is a nullable FK and is not unique in canonical migration.
- `RegistrationService` does not populate or approve `users.account_id`; it retains an explicit Kimia-link TODO.
- Registration and `UserObserver` both create Wallet/default accounts: **DUPLICATE CANDIDATE**; not silently repaired in this slice.
- no dedicated authenticated Customer→Kimia resolver is evidenced: **NOT IMPLEMENTED**.
- no active canonical Tenant root is evidenced: **NOT IMPLEMENTED**.
- exact Tenant/Company/Connector/Book identity/routing semantics: **BLOCKED BY GROUND TRUTH**.
- previously cited ADR-024/ADR-026 detailed rules: **BLOCKED BY SOURCE RECOVERY** until exact source is recovered.

## Reconciliation implementation

Current V2-01 files:

- `backend/app/Services/Kimia/CustomerAccountReconciliationService.php`
- `backend/app/Console/Commands/KimiaInspectAccountReconciliation.php`
- `backend/tests/Feature/KimiaInspectAccountReconciliationTest.php`
- `docs/v2/59_V2_01_CANONICAL_BINDING_INVENTORY.md`
- `docs/v2/60_V2_01_BINDING_TENANT_TRACEABILITY_GATE.md`

The service/command are read-only. Tests prove no mutation and prove duplicate bindings are reported rather than repaired.

## Validation evidence

Exact head `058680093fea90a30235acc1171c744a3c472ca1`:

- Backend RC1 Validation #437 — **EXECUTED — PASS**
- Operational Readiness #47 — **EXECUTED — PASS**

Exact head `1b7380abc688b4fea295176ffd759e3396164b71`:

- Backend RC1 Validation #440 — **EXECUTED — PASS**
- Operational Readiness #50 — **EXECUTED — PASS**

Read-only reconciliation classification: **TESTED — NOT MERGED**.

Subsequent source-integrity documentation commits require their own exact-head CI before a newer PR-level PASS claim.

## Target canonical resolution chain

The proven minimum future chain is:

`Authenticated Customer -> approved platform binding -> exact local Account -> exact Kimia AccountId -> canonical Kimia Read -> reconciliation metadata -> customer-safe financial presentation`

Any White-label Tenant/Company/Connector/Book context required by the final recovered architecture must be inserted into this chain only after it is grounded.

No link may be invented.

## Activation prerequisites

Before activating Customer financial resolution:

1. the approved workflow that populates `users.account_id` must be grounded and implemented without inference;
2. referenced Account must exist and expose its exact canonical `kimia_id`;
3. duplicate/orphan/ambiguous states must fail closed under the final accepted cardinality rules;
4. any required White-label identity/routing context must have exact Ground Truth and backend enforcement;
5. no hard-coded/sample/fallback Kimia AccountId may exist;
6. no Kimia Write is required or authorized by this resolver.

## Exit criteria

V2-01 may only close when all applicable items are evidenced:

- canonical resolver path implemented from verified relationships/context;
- no hard-coded/sample/fallback Kimia AccountId;
- reconciliation remains read-only and tested;
- duplicate/orphan/ambiguous states reported, never silently fixed;
- Customer Money/Gold/Coin/Currency remain Kimia-authoritative;
- Controller does not call Kimia client directly;
- Kimia Write remains disabled;
- any required White-label context is canonical and enforced;
- API/OpenAPI changes, if any, are synchronized;
- exact-head tests/CI recorded;
- runtime reconciliation evidence collected from an approved environment or explicitly remains a blocking exit criterion;
- documentation and traceability complete.

## Current known external blocker

An upstream Composer security advisory involving `league/commonmark 2.8.3` can independently fail Security Hardening / RC2 dependency audit. It must not be suppressed or worked around by weakening security.

## Current capability status

- V2-01 charter: **TESTED — NOT MERGED** for the last exact-head validated state.
- Read-only reconciliation: **TESTED — NOT MERGED**.
- Canonical Customer↔Kimia resolver: **NOT IMPLEMENTED**.
- Approved binding workflow: **NOT IMPLEMENTED**.
- Exact historical binding cardinality/immutability rules previously attributed to ADR-024: **BLOCKED BY SOURCE RECOVERY**.
- White-label product requirement: **REUSE AS-IS**.
- Canonical Tenant runtime root: **NOT IMPLEMENTED**.
- Tenant/Company/Connector/Book identity/routing semantics: **BLOCKED BY GROUND TRUTH**.
- Customer financial read current fail-closed behavior: **REUSE AS-IS**.
- Kimia Write: **BLOCKED BY GROUND TRUTH**.
