# GoldPlatform V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification

**Status:** IN PROGRESS — RECONCILIATION TESTED — RESOLVER BLOCKED  
**Base branch:** `recovery/rc2-product-rebuild`  
**Base exact SHA:** `d9ee5fee69969fa02ac25c96d8e1653143ba413b`  
**Working branch:** `v2/v2-01-canonical-runtime-integration`

## Outcome

Establish a canonical, read-only, fail-closed runtime path that can prove how an authenticated GoldPlatform customer resolves to the correct Kimia account and how Money/Gold/Coin/Currency reads are reconciled against Kimia without creating a competing customer balance source.

V2-01 is not a rewrite of existing foundations. Existing canonical capabilities must be reused where valid.

## Current result

The canonical source proves a structural `User.account_id -> Account.kimia_id` path and contains canonical Kimia read repositories. It does not contain an approved runtime workflow that populates `users.account_id`, and it does not yet contain the accepted Tenant/connector runtime root required for a White-label financial resolver.

A narrowly reconstructed read-only reconciliation capability is now present and tested. It reports conflicts and performs no repair. Customer financial endpoints remain intentionally fail-closed.

## Why this is a distinct stage

Evidence review established a non-duplicate integration outcome:

- canonical Kimia Read foundations already exist and must be reused;
- Customer financial controllers are intentionally fail-closed rather than exposing Wallet/Ledger projections;
- PR #196 contains reusable reconciliation concepts but is materially diverged and is not a direct merge/cherry-pick source;
- the approved Customer↔Kimia binding rule exists in project Ground Truth, but current canonical runtime enforcement is incomplete;
- accepted White-label/Multi-tenant direction exists, but a canonical Tenant/connector identity root is not implemented.

## Source-of-truth boundaries

1. **Kimia is final authority** for customer Money, Gold, Coin and Currency balances.
2. GoldPlatform must not create, infer, repair, or promote a competing financial balance.
3. GoldPlatform remains final authority for physical Custody/Amanat only.
4. Internal Ledger / Projection / Reservation structures are audit/workflow/reconciliation aids only.
5. Kimia Write remains disabled/deny-by-default.

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
- silent Tenant/Connector/Permission redesign;
- broad cherry-pick/merge from PR #196;
- migration creation merely to force an assumed binding model.

## Canonical findings

- `accounts.kimia_id` is non-null and unique in the canonical migration.
- `users.account_id` is a nullable FK and is not unique in the canonical migration.
- `RegistrationService` does not populate or approve `users.account_id`; it retains an explicit Kimia-link TODO.
- Registration and `UserObserver` both create Wallet/default accounts: **DUPLICATE CANDIDATE**; not silently repaired in this slice.
- Accepted historical Ground Truth defines one login -> zero/one Account -> zero/one Kimia AccountId, and no Kimia AccountId shared by multiple logins.
- Historical unique-binding migration and immutability guards were described as prepared, not canonical runtime implementation: **HISTORICAL ONLY**, candidate **REUSE AFTER FIX**.
- Accepted architecture defines GoldPlatform as White-label/Multi-tenant, but no active canonical Tenant root is evidenced: **NOT IMPLEMENTED**.
- Tenant -> Kimia connector/book runtime mapping remains **BLOCKED BY GROUND TRUTH**.

## Reconciliation implementation

Current V2-01 files:

- `backend/app/Services/Kimia/CustomerAccountReconciliationService.php`
- `backend/app/Console/Commands/KimiaInspectAccountReconciliation.php`
- `backend/tests/Feature/KimiaInspectAccountReconciliationTest.php`
- `docs/v2/59_V2_01_CANONICAL_BINDING_INVENTORY.md`
- `docs/v2/60_V2_01_BINDING_TENANT_TRACEABILITY_GATE.md`

The service/command are read-only. Tests prove no mutation and prove duplicate bindings are reported rather than repaired.

## Validation evidence

Reconciliation exact head:

`058680093fea90a30235acc1171c744a3c472ca1`

- Backend RC1 Validation #437 — **EXECUTED — PASS**
- Operational Readiness #47 — **EXECUTED — PASS**

Reconciliation classification: **TESTED — NOT MERGED**.

Subsequent documentation-only commits require their own exact-head CI status before any broader PR-level PASS claim.

## Target canonical resolution chain

The eventual chain is evidence-driven and fail-closed:

`Authenticated Customer -> verified Tenant context -> approved platform binding -> exact Tenant Kimia connector/book context -> exact Kimia AccountId -> canonical Kimia Read -> reconciliation metadata -> customer-safe financial presentation`

No link may be invented.

## Activation prerequisites

Before activating a Customer financial resolver:

1. canonical Tenant identity/context must exist under the accepted White-label architecture;
2. authenticated User -> Tenant equality must be backend-enforced;
3. the approved workflow that populates `users.account_id` must be implemented without inference;
4. duplicate/orphan/ambiguous bindings must fail closed;
5. Tenant -> Kimia connector/book mapping must be exact when required;
6. historical unique-binding/immutability enforcement must be reconstructed only after comparison with current schema and Tenant semantics;
7. no Kimia Write is required or authorized by the resolver.

## Exit criteria

V2-01 may only close when all applicable items are evidenced:

- canonical resolver path is implemented from verified relationships/context;
- no hard-coded/sample/fallback Kimia AccountId exists;
- reconciliation remains read-only and tested;
- duplicate/orphan/ambiguous states are reported, never silently fixed;
- Customer Money/Gold/Coin/Currency remain Kimia-authoritative;
- Controller does not call Kimia client directly;
- Kimia Write remains disabled;
- Tenant/connector context required by the accepted architecture is canonical and enforced;
- API/OpenAPI changes, if any, are synchronized;
- exact-head tests/CI are recorded;
- runtime reconciliation evidence is collected from an approved environment or explicitly remains a blocking exit criterion;
- documentation and traceability are complete.

## Current known external blocker

An upstream Composer security advisory involving `league/commonmark 2.8.3` can independently fail Security Hardening / RC2 dependency audit. It must not be suppressed or worked around by weakening security.

## Current capability status

- V2-01 charter: **TESTED — NOT MERGED** for its prior exact-head validation baseline.
- Read-only reconciliation: **TESTED — NOT MERGED**.
- Canonical Customer↔Kimia resolver: **NOT IMPLEMENTED**.
- Approved binding workflow: **NOT IMPLEMENTED**.
- Historical unique-binding / immutability implementation: **HISTORICAL ONLY**, candidate **REUSE AFTER FIX**.
- Canonical Tenant runtime root: **NOT IMPLEMENTED**.
- Tenant -> Kimia connector/book mapping: **BLOCKED BY GROUND TRUTH**.
- Customer financial read current fail-closed behavior: **REUSE AS-IS**.
- Kimia Write: **BLOCKED BY GROUND TRUTH**.
