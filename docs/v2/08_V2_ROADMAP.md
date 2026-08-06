# GoldPlatform V2 — Controlled Roadmap

Status: IN PROGRESS — V2-00 roadmap baseline
Owner: Alireza Bahramian

## 1. Roadmap principle

GoldPlatform V2 is a controlled reconstruction, not a blind rewrite.

Every stage must follow:

`Requirement → Source → Ground Truth → Architecture → Database → Backend → API → OpenAPI → Frontend → Permission → Audit → Idempotency → Tests → exact-SHA CI → PR/Merge → Visual Verification → Documentation`

No stage may claim completion when this chain is materially incomplete.

## 2. Global safety gates

The following block implementation or activation:
- Unknown financial rule
- Unknown Kimia Write payload or endpoint behavior
- Conflicting accepted sources
- Unresolved sensitive CI failure
- Destructive migration/history action
- Unconfirmed tenant/company/branch architecture
- Missing idempotency or reconciliation for sensitive operations

Preserve-first restrictions remain active for force push, hard reset, shared-history rebase, broad revert, blind cherry-pick, branch deletion and applied migration rewrite.

## 3. Stage V2-00 — Complete Source Recovery and Knowledge Reconstruction

### Goal
Create the single traceable knowledge baseline for V2.

### Required outputs
- `00_SOURCE_INDEX.md`
- `01_MASTER_REQUIREMENTS.md`
- `02_BUSINESS_RULE_REGISTRY.md`
- `03_KIMIA_GROUND_TRUTH.md`
- `04_CAPABILITY_TRACEABILITY_MATRIX.md`
- `05_ARCHITECTURE_CONTRACT.md`
- `06_IMPLEMENTATION_AUDIT.md`
- `07_GAP_AND_DRIFT_REPORT.md`
- `08_V2_ROADMAP.md`
- `09_DECISION_LOG.md`
- `10_PROJECT_STATE.md`

### Exit gates
- All 11 documents exist and are internally consistent
- Current reference Branch/Base/Head/PR are recorded
- CI passes on the exact final documentation Head SHA
- Capability matrix covers all major domains
- Known conflicts and missing sources are explicit
- No Feature, financial rule, Kimia Write or migration is introduced

### Current status
`IN PROGRESS — FINAL EVIDENCE GATE PENDING`

## 4. Stage V2-01 — Canonical Repository and Traceability Closure

### Goal
Turn the initial knowledge baseline into a complete canonical evidence ledger.

### Scope
- Inventory all reachable branches and Heads
- Inventory all Open, Draft, Closed and Merged PRs
- Classify Closed — Not Merged work as historical evidence
- Map PR Head SHA, merge SHA and exact CI
- Build capability-to-file/PR/SHA/test/CI links
- Resolve duplicate documentation paths and ADR number drift by classification, not deletion
- Record applied migration state where evidence is available

### Prohibited
- Blind integration of historical stacks
- Destructive cleanup
- New financial or product capability

### Exit gates
- Repository/PR ledger complete enough to answer where each major capability came from
- Duplicate candidates classified
- Canonical implementation path identified per capability
- Exact-SHA CI gaps listed honestly

## 5. Stage V2-02 — Kimia Read and Customer Account Resolution

### Goal
Provide a reliable, observable, read-only financial data path from authenticated customer to Kimia.

### Scope
- Customer-to-Kimia account mapping contract
- Account and group reads
- Money and Gold balance reads
- Dynamic Coin and Currency catalogs
- Coin/Currency balance source verification
- Sanitized raw-response fixtures
- Read error taxonomy
- Sync timestamp and rebuildable snapshot contract where needed
- Reconciliation-read foundation

### Safety
- No Kimia Write
- No internal final balance
- No unavailable-to-zero fallback
- No hard-coded product/account/group identifiers

### Exit gates
- Authenticated test customer resolves to verified Kimia AccountId
- Customer Money/Gold/Coin/Currency reads have traceable source contracts
- Negative balances are tested
- Exact-SHA CI and OpenAPI pass
- Customer frontend visual states verified

## 6. Stage V2-03 — Identity, Authentication and Customer Foundation

### Goal
Close customer identity, session and profile foundations without inventing KYC or security rules.

### Scope
- OTP send/verify contract
- Abuse/rate-limit controls
- Session/token lifecycle
- Logout/revocation
- Customer profile reads
- KYC read/write only when Ground Truth is confirmed
- Safe error/no-store/request-id behavior

### Exit gates
- Authentication E2E passes
- Security and privacy review passes
- Profile and session contracts are documented and visually verified

## 7. Stage V2-04 — Pricing and Quote Ground Truth

### Goal
Establish the accepted pricing engine and immutable quote contract.

### Required decisions/evidence
- Price source and freshness
- Buy/sell formulas
- Commission and spread
- Rounding
- Minimum/maximum order
- Freeze and anti-scalping windows
- Customer-group exceptions
- Credit-related eligibility
- Rial/Toman boundary

### Scope after approval
- Central Backend pricing service
- Immutable Quote DTO/model
- Expiry and replay protection
- Audit and trace inputs
- OpenAPI and customer UI quote states

### Exit gates
- Owner-approved rules are in Rule Registry
- Decimal tests pass
- Frontend contains no financial calculation
- Quote replay/expiry tests and exact-SHA CI pass

## 8. Stage V2-05 — Order Lifecycle and Trading Intent

### Goal
Provide a complete order lifecycle before enabling external financial execution.

### Scope
- Buy/Sell order creation
- State machine and transitions
- Quote binding
- Idempotency
- Permission and ownership
- Cancellation/expiry rules
- Audit and outbox intent
- Customer, Operator and Admin read views

### Safety
- Kimia Write remains disabled until V2-06 Ground Truth gate passes
- Internal Ledger is not final settlement evidence

### Exit gates
- State machine and ownership tests pass
- API/OpenAPI/frontend are aligned
- Visual verification completed

## 9. Stage V2-06 — Kimia Write Adapter and Controlled Settlement

### Goal
Implement the smallest approved Kimia Write slice with fail-closed controls.

### Preconditions
- Real endpoint/payload/action/product evidence
- RequestId behavior confirmed
- Error/retry behavior confirmed
- Security review complete
- Owner approval recorded

### Scope
- Separate Write client/gateway
- Domain commands without raw Kimia codes
- Central endpoint-specific mapping
- Intent/outbox/idempotency registry
- Controlled retry policy
- External Voucher/Record evidence storage
- Post-write balance readback
- Settlement result classification
- Incomplete/duplicate operation detection

### Exit gates
- Sandbox/approved real test evidence
- Duplicate prevention proved
- Reconciliation and readback proved
- Failure injection tests pass
- Exact-SHA CI and owner decision recorded

## 10. Stage V2-07 — Reconciliation and Financial Operations

### Goal
Make external financial operations traceable and recoverable.

### Scope
- Intent/result reconciliation
- Missing/duplicate Voucher and Record detection
- Balance discrepancy detection
- Operator/Admin investigation views
- Safe retry/manual resolution workflow
- Immutable audit trail
- Reports for unresolved discrepancies

### Exit gates
- Reconciliation drill passes
- No silent auto-correction
- Permissions and four-eyes controls are accepted where needed

## 11. Stage V2-08 — Physical Products, Custody and Inventory

### Goal
Complete physical asset lifecycle independently from financial balances.

### Scope
- Dynamic product/category definitions
- Parsian, bullion, melted gold and other accepted categories
- Custody creation
- Custody conversion and resale
- Physical inventory
- Branch scope
- Delivery request/approve/ready/complete
- Barcode/RFID integration only with Ground Truth

### Exit gates
- Product-specific rules approved
- Inventory/custody ownership tests pass
- Physical operational drill and visual verification pass

## 12. Stage V2-09 — Admin and Operator Completion

### Goal
Provide safe operational workspaces for all accepted capabilities.

### Scope
- Canonical permission catalog
- User/group management reads
- Orders, settlements, custody and delivery operations
- Audit, outbox and reconciliation
- Reports and exports based only on reliable data
- Notifications/templates after channel Ground Truth
- Monitoring and controlled operational actions

### Exit gates
- Every route/action has explicit Backend permission
- Sensitive responses are allowlisted
- Operator workflows are visually and operationally verified

## 13. Stage V2-10 — White-label and Tenant Architecture

### Goal
Introduce accepted white-label and isolation architecture without leaking financial complexity to UI.

### Preconditions
- Owner decision on tenant/company/branch hierarchy
- Data ownership and Kimia configuration boundaries confirmed

### Scope
- Tenant/company/branch model
- Domain and branding configuration
- Theme/logo/locale
- Tenant-scoped permissions and data
- Tenant-specific integration configuration with secret isolation

### Exit gates
- Cross-tenant isolation tests pass
- Migration and rollback strategy approved
- White-label visual verification passes

## 14. Stage V2-11 — Cross-platform Product Experience

### Goal
Deliver approved Customer, Operator and Admin experiences on supported platforms.

### Scope
- High-fidelity customer page approval first
- Responsive web/PWA completion
- Android/iOS strategy decision
- Windows delivery strategy decision
- Loading/empty/error/forbidden/offline states
- Accessibility, RTL and device testing

### Safety
- PWA offline does not cache sensitive financial APIs or execute financial mutations
- Native status is reported honestly

### Exit gates
- Device matrix visual verification
- Accessibility and usability checks
- No internal accounting terminology or frontend financial logic

## 15. Stage V2-12 — Production Readiness and Release

### Goal
Prove the exact release SHA is operationally ready for the target environment.

### Scope
- Environment and secret management
- Production compose/runtime validation
- Database migration plan
- Queue/scheduler/outbox controls
- Monitoring and alert delivery
- Backup and restore drill
- Security review
- Performance/load checks
- Deployment, rollback and incident runbooks
- Final traceability closure

### Exit gates
- All required CI passes on exact release SHA
- Production environment evidence recorded
- Backup restore drill passes
- Monitoring and rollback verified
- Remaining risk accepted by owner

Only after these gates may `Production Ready` be considered.

## 16. Prioritization rule

Work should proceed in this order unless a newly discovered Ground Truth dependency changes it:

1. Knowledge and traceability
2. Kimia Read and account resolution
3. Identity/customer foundation
4. Pricing and Quote
5. Order lifecycle
6. Kimia Write and settlement
7. Reconciliation
8. Custody/inventory/delivery
9. Admin/operator completion
10. Tenant/white-label
11. Cross-platform experience
12. Production release

## 17. Current next action

Complete V2-00 exact-SHA CI and evidence Gate. Do not start V2-01 merely because the documents exist; the final Head, PR status, unresolved evidence gaps and Gate result must be recorded in `10_PROJECT_STATE.md` and the PR discussion.