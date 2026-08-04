# Phase 0 — Slice and Dependency Matrix

> Status: In Progress
>
> Date: 2026-08-05
>
> Base: `main@31d55fac545201c7b436e940e48e9dcd89bd553d`
>
> Recovery branch: `recovery/phase-0-current-state`

## Purpose

This document converts historical PRs and branches into explicit integration slices. Historical branches are evidence sources only. No branch listed here is approved for direct merge, retarget, rebase or blind cherry-pick.

Canonical method:

`Preserve → Inspect → Compare → Validate → Document → Rebuild Clean Slice → Integrate`

## Slice Ordering

Recommended reconstruction order:

1. Recovery governance and exact-SHA regression proof
2. Order contract baseline and lifecycle decision
3. Trading Stage 03 clean reconstruction
4. Customer backend recovery against the selected Order contract
5. Canonical permission catalog
6. Backoffice session bootstrap
7. Admin/Operator read APIs
8. AP-06 customer-policy approval workflow
9. Customer frontend shell
10. Admin/Operator frontend shell

The order is dependency-driven, not roadmap-number-driven.

---

## S00 — Recovery Governance and Baseline Proof

### Source

- `main@31d55fac...`
- PR #145 recovery branch

### Scope

- Recovery documents
- Git/PR evidence table
- exact-SHA CI proof
- backend baseline regression
- source-of-truth guards

### Production files

- No product feature files

### Required tests

- Composer validate
- PHP syntax
- migration fresh
- full PHPUnit
- exact-SHA workflow status
- secret scan

### Status

- Documentation: **Implemented — Not Tested**
- Baseline regression: **WRITTEN — NOT EXECUTED**

---

## S01 — Order Contract Baseline

### Current main evidence

- `backend/app/Models/Order.php`
- `backend/database/migrations/2026_07_15_140347_create_orders_table.php`

Current persisted statuses:

- `pending`
- `paid`
- `processing`
- `completed`
- `cancelled`

### Historical product-line evidence

- `backend/app/Enums/OrderStatus.php`
- `backend/app/Services/Order/OrderStateMachine.php`
- state-machine migrations from the historical product line
- Customer order status contract and tests

### Stage 03 evidence

- `backend/app/Domain/Trading/Enums/OrderStatus.php`
- `backend/app/Domain/Trading/Order/Order.php`

Stage 03 statuses:

- `draft`
- `submitted`
- `approved`
- `rejected`
- `expired`
- `cancelled`

### Conflict

The current `main` persistence contract and Stage 03 domain contract are not directly compatible. A mapping or a single accepted lifecycle is required before Stage 03 persistence is reconstructed.

### Required decision type

Technical contract consolidation. No new business rule may be invented.

### Required tests

- migration compatibility
- existing-row compatibility
- state transition unit tests
- Customer API status regression
- Admin/Operator queue filters
- idempotency and concurrency

### Status

**Needs Decision / Architecture Drift Risk**

---

## S02 — Trading Stage 03 Domain Foundation

### Source

PR #109 — 51 changed files.

### Production slice

#### Contracts

- `TenantScopedOrderRepository.php`
- `TenantScopedQuoteRepository.php`

#### Enums and value objects

- `OrderDecision.php`
- `OrderStatus.php`
- `QuoteStatus.php`
- `OrderId.php`
- `QuoteId.php`

#### Quote and Order domain

- `Quote.php`
- `Order.php`
- command objects
- idempotent create/submit/decision/terminal services

#### Validation

- validation contexts
- validation engines
- matching-scope rule
- quote/order consistency rule
- submitted-order rule

#### Persistence

- database order repository
- database quote repository
- in-memory repositories
- Trading service provider
- provider registration

#### Migration

- `2026_08_04_220000_create_tenant_scoped_trading_tables.php`

### Tests that must travel with production code

- `AtomicCreateOrderFromQuoteTest.php`
- `AtomicCreateOrderRollbackTest.php`
- `IdempotentSubmitOrderTest.php`
- `TradingDatabaseRepositoriesIntegrationTest.php`
- `TradingSchemaTest.php`
- `IdempotentOrderDecisionServiceTest.php`
- `OrderLifecycleTest.php`
- `QuoteLifecycleTest.php`
- `TradingValidationEngineTest.php`

### Documents that must travel with the slice

- five Trading ADRs
- Stage 03 persistence audit
- Project State
- CHANGELOG

### Dependencies

- Stage 02 Financial Scope and idempotency contracts
- accepted S01 Order contract
- MySQL transaction behavior
- application/provider registration

### Explicit exclusions

- pricing formulas
- freeze duration
- customer credit limits
- Kimia write mapping
- final customer financial balances

### Integration rule

This is one production-plus-test slice. Test-only commits must not be moved independently.

### Status

**In Progress / Clean Reconstruction Required**

---

## S03 — Canonical Permission Foundation

### Source A — AP-01, PR #104

Exact PR files:

- `AdminOperatorPermissionCatalog.php`
- `bootstrap/app.php`
- `AdminOperatorPermissionSeeder.php`
- `DatabaseSeeder.php`
- `routes/api.php`
- `AdminOperatorPermissionFoundationTest.php`
- `AP-01_ADMIN_FOUNDATION.md`

### Source B — OP chain

OP-02 intentionally avoids changing the Permission Seeder and consumes effective Spatie permissions.

### Risks

- conflicting permission names
- destructive `syncPermissions` behavior
- implicit removal of direct permissions
- route middleware conflicts
- operator over-privilege

### Canonical permission names requiring consolidation

- `audit.view` vs `audit-logs.view`
- `orders.queue.view` vs `orders.view`
- `deliveries.complete` vs `deliveries.deliver`
- `kimia.read` vs `kimia.view`

### Required implementation shape

- one catalog
- additive, non-destructive seeding by default
- explicit role assignments
- no future Kimia-write or balance-adjustment permission without Ground Truth
- backend authorization remains authoritative

### Required tests

- seed on empty database
- seed on populated database
- re-run idempotency
- preservation of custom/direct permissions
- role boundary tests
- IDOR tests
- route middleware coverage

### Status

**Duplicate Candidate / Needs Consolidation**

---

## S04 — Backoffice Session Bootstrap

### Source

PR #141 — OP-02.

Exact files:

- `AdminBootstrapController.php`
- `OperatorBootstrapController.php`
- `BackofficeSessionBootstrap.php`
- `BackofficeSessionBootstrapContractTest.php`
- `AdminOperatorFoundationTest.php`
- `OP-02-SESSION-BOOTSTRAP.md`
- `backoffice-v1.openapi.yaml`

### Dependencies

- canonical S03 permission catalog
- authenticated session
- Spatie roles and effective permissions
- accepted versioned route file

### Safety boundaries

- navigation is not authorization
- masked user data only
- no financial calculation
- no Kimia call
- no permission mutation

### Required tests

- unauthenticated 401
- wrong-role 403
- effective role and permission output
- masked mobile
- navigation filtering
- OpenAPI contract

### Status

**Reusable Candidate / Rebuild on Clean Base**

---

## S05 — Customer Policy Approval Workflow

### Source

PR #114 — AP-06.

Exact files:

- `CustomerPolicyChangeStatus.php`
- `CustomerPolicyChangeRequestController.php`
- `CustomerPolicyChangeRequest.php`
- `CustomerPolicyChangeRequestService.php`
- `AdminOperatorPermissionCatalog.php`
- `2026_08_04_180500_create_customer_policy_change_requests_table.php`
- `admin_operator_v1.php`
- `CustomerPolicyChangeApprovalFoundationTest.php`
- AP-06 documentation

### Value

Creates a proposal/submit/approve/reject workflow without applying a financial policy automatically.

### Dependencies

- canonical S03 permissions
- existing `CustomerTradingPolicy`
- Audit
- Outbox
- Idempotency
- route and response contract

### Critical safety invariant

Approval must not mutate the active trading policy. There is no Apply endpoint until an accepted rule exists.

### Migration tests

- migrate:fresh
- forward migration
- rollback
- migrate again
- MySQL indexes and uniqueness
- existing policy compatibility

### Application tests

- draft → submitted
- submitted → approved/rejected
- invalid transitions
- idempotency replay
- row-lock/concurrency
- approval does not apply policy
- permission and IDOR

### Status

**High-Value Candidate / WRITTEN — NOT EXECUTED**

---

## S06 — Customer Frontend Shell

### Source

PR #140 — FE-02.

Exact files:

- customer frontend workflow
- frontend technology ADR
- FE-02 documentation
- `frontend/customer-app/` Nuxt application files

### Dependencies

- recovered Customer API Contract
- authentication/bootstrap contract
- selected Order status contract
- runtime white-label config

### Safety boundaries

- no financial formula
- no direct Kimia call
- no hard-coded Kimia identifier
- no automatic retry for mutations

### Required tests

- dependency install
- typecheck
- lint
- production build
- API contract tests
- responsive and RTL
- accessibility
- browser E2E

### Status

**Implemented — Not Tested / Clean Rebuild Required**

---

## S07 — Admin/Operator Frontend Foundation

### Source A

PR #137 — AP-20, `frontend-admin/`.

Exact files:

- app shell
- CSS
- API/session composables
- Nuxt config and package
- admin and operator pages
- AP-20 document

### Source B

OP-03 and later application shell/dashboard/queue chain.

### Conflict

Both are frontend foundations for the same domain and cannot be accepted in parallel.

### Selection criteria

- session bootstrap compatibility
- no hard-coded roles/permissions
- typed API contracts
- no financial logic
- build/typecheck success
- route guard behavior
- RTL/responsive quality
- simplest maintainable structure

### Required tests

- install
- typecheck
- lint
- build
- component tests
- permission navigation
- 401/403 handling
- browser E2E

### Status

**Parallel Development Conflict / Select One Foundation**

---

## S08 — Admin/Operator Read APIs

### Candidate capabilities

- dashboards
- operational queues
- user read
- customer-group read
- roles/permissions read
- order read
- custody/delivery read
- settlement read
- Kimia overview
- system health
- product/pricing read
- branch projection
- white-label discovery
- notification overview
- reports catalog

### Source

AP-02 through AP-17 and OP-04/OP-05.

### Integration rule

Each endpoint is an independent capability slice. It must be compared against current routes and existing read models before reconstruction.

### Required checks per endpoint

- canonical route
- permission name
- response envelope
- pagination/filter contract
- no model serialization leakage
- no PII leakage
- no raw metadata/Kimia payload
- tenant/company safety statement
- OpenAPI and regression

### Status

**Duplicate Candidate / Slice-by-Slice Reconstruction**

---

## Global Dependency Graph

```text
S00 Recovery & Regression Proof
  └─ S01 Order Contract Baseline
       ├─ S02 Trading Stage 03
       │    └─ Customer Order APIs / Settlement continuation
       └─ Customer Backend Recovery

S03 Canonical Permissions
  ├─ S04 Backoffice Session Bootstrap
  ├─ S05 Customer Policy Approval
  └─ S08 Admin/Operator Read APIs
       └─ S07 Admin/Operator Frontend

Recovered Customer Backend
  └─ S06 Customer Frontend
```

## First Implementation Candidate After Recovery

The first low-risk code slice should not be Stage 03 itself. The recommended first implementation candidate is:

**S03 Canonical Permission Catalog — evidence and non-destructive contract only**

Reason:

- small scope
- no financial rule
- no Kimia write
- unlocks Session Bootstrap and Admin/Operator APIs
- can be tested independently

However no product slice should start until exact-SHA baseline regression for the chosen reconstruction base has been established.

## Remaining Evidence Gaps

- exact current code of historical Customer Closure must be compared file-by-file against the reconstruction base
- exact AP/OP route overlaps must be enumerated
- CI runs for historical SHAs must be captured where available
- migration behavior has not been executed in this recovery environment
- frontend dependencies have not been installed

## Test Truth

- Matrix construction: **EXECUTED — PASS**
- Production tests: **NOT EXECUTED**
- Migration tests: **NOT EXECUTED**
- Frontend tests: **NOT EXECUTED**
- exact-SHA CI: **NOT CONFIRMED**
