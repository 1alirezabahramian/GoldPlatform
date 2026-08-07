# GoldPlatform V2 — Master Requirements

Status: IN PROGRESS — V2-00 recovery baseline
Owner: Alireza Bahramian
Evidence branch: `recovery/rc2-product-rebuild`
V2 working branch: `v2/source-recovery-v2-00`

## 1. Purpose

This document is the canonical requirements baseline for GoldPlatform V2. V1 remains preserved as evidence, history, code and prior decisions. V2 is not a blind rewrite; it is a controlled reconstruction based on traceable evidence.

Required method:

`Preserve → Inspect → Inventory → Extract → Compare → Validate → Classify → Document → Plan → Integrate → Continue`

## 2. Source hierarchy

### Financial and Kimia rules
1. Real Kimia API output
2. Official Kimia Swagger/OpenAPI
3. Accepted Project Memory and ADRs
4. Owner-confirmed rules and examples
5. Valid canonical GitHub code
6. General knowledge for explanation only

### Development status
1. Current GitHub state
2. Real branch, commit and PR metadata
3. CI on the exact SHA
4. Documentation on the same branch
5. Project Memory and accepted ADRs
6. Old chats and ZIP files as historical evidence only

## 3. Non-negotiable system boundaries

### MR-V2-0001 — Kimia financial authority
Kimia is the final source of truth for customer Money, Gold, Coin and Currency balances. GoldPlatform must not create an independent or competing final balance for these assets.

### MR-V2-0002 — Custody authority
GoldPlatform is the final source of truth for physical Custody/Amanat. Custody must remain separate from financial balances and wallet projections.

### MR-V2-0003 — Internal financial evidence
Ledger, Journal, Event Store, Idempotency Registry and Balance Projection may support audit, trace, intent/result, order lifecycle, settlement workflow and reconciliation. They are not the customer's final financial balance authority.

### MR-V2-0004 — Decimal safety
Money and weight must use exact Decimal or decimal strings. Float is prohibited for financial and weight calculations.

### MR-V2-0005 — Rial/Toman boundary
Kimia uses Rial while the product may display Toman. Conversion must be centralized, explicit, tested and performed in Backend only.

### MR-V2-0006 — Frontend simplicity
Frontend must not calculate financial amounts, Weight750, balances or Rial/Toman conversion. It must present validated Backend contracts in Persian, RTL and mobile-first form.

### MR-V2-0007 — Dynamic products
Coin and Currency catalogs must be read dynamically from Kimia or an explicitly rebuildable Kimia-derived snapshot. Sample identifiers must never be hard-coded as universal truth.

### MR-V2-0008 — Kimia read/write separation
Kimia Read and Kimia Write must be separate infrastructure paths with different retry and failure policies.

### MR-V2-0009 — Kimia Write fail-closed
No Kimia Write may be activated without real Ground Truth for endpoint, payload, identifiers, Action mapping, Transaction mapping, idempotency, error behavior and post-write reconciliation.

### MR-V2-0010 — Post-write readback
After a successful Kimia Write, the relevant customer balance must be read again from Kimia. Internal projections may be reconciled but must not replace that readback.

### MR-V2-0011 — Layering
Controllers must not call Kimia clients directly. Required flow:

`Controller → Application Service → Domain Policy/Workflow → Repository/Gateway → Kimia Client`

### MR-V2-0012 — Security
Credentials, tokens, passwords, API keys, raw sensitive payloads and customer-sensitive data must not appear in Git, public logs, UI or documentation.

## 4. Customer requirements

### MR-V2-0101 — Authentication
Mobile-first authentication and OTP flows must be explicit, rate-limited, auditable and backed by an accepted session/token contract.

### MR-V2-0102 — Profile and KYC
Profile reads must expose only approved customer fields. KYC writes and identity verification rules require confirmed business and regulatory Ground Truth.

### MR-V2-0103 — Dashboard
Dashboard financial balances must ultimately come from Kimia. Unavailable financial data must be shown as unavailable, never silently replaced by zero or internal wallet values.

### MR-V2-0104 — Asset views
Money, Gold, Coin and Currency must be presented separately and may be positive, zero or negative. Custody must appear in a separate physical-assets section.

### MR-V2-0105 — Customer language
Customer UI must not expose Voucher, RecordId, AccountId, Action Code, Debit/Credit, Ledger or internal accounting terminology.

## 5. Trading requirements

### MR-V2-0201 — Pricing
Pricing formula, source price, spread, commission, rounding, floor/ceiling and validity windows require traceable owner-approved Ground Truth.

### MR-V2-0202 — Quote
Quote must be immutable for its validity window, traceable, auditable and linked to its pricing inputs and customer context.

### MR-V2-0203 — Buy/Sell
Buy and Sell must use exact decimals, accepted quote evidence, permissions, idempotency, audit and settlement workflow. No final customer balance decision may rely only on internal Wallet or Ledger.

### MR-V2-0204 — Freeze and anti-scalping
Freeze duration, anti-scalping window, customer-group exceptions and limits must be recovered from accepted sources before implementation or reactivation.

### MR-V2-0205 — Credit
Credit limit, negative-balance policy, blocking and override behavior require owner-confirmed rules and explicit authorization.

### MR-V2-0206 — Settlement
Settlement completion requires verified external result evidence where Kimia is involved. Ledger balance alone is insufficient proof of completion.

### MR-V2-0207 — Reconciliation
Every sensitive financial operation must support intent/result correlation, duplicate detection, incomplete-operation detection and reconciliation against Kimia Voucher/Record evidence.

## 6. Physical assets and delivery

### MR-V2-0301 — Custody lifecycle
Custody creation, conversion, resale and delivery must be modeled independently from financial balances.

### MR-V2-0302 — Product categories
Parsian, bullion, melted gold, jewelry and other physical categories require explicit product definitions and accepted conversion rules.

### MR-V2-0303 — Delivery
Delivery must include controlled transitions, ownership checks, branch/inventory scope, audit, idempotency and safe customer/operator responses.

### MR-V2-0304 — Inventory
Physical inventory must be distinct from Kimia customer balance authority and scoped by the confirmed branch/company/tenant model.

## 7. Admin and operator requirements

### MR-V2-0401 — Authorization
Backend permission checks are authoritative. Frontend navigation visibility is not authorization.

### MR-V2-0402 — Least privilege
Admin and Operator actions must use explicit permissions. Sensitive actions require dedicated permissions and auditable transitions.

### MR-V2-0403 — Safe read models
Admin and Operator APIs must use explicit allowlists and must not serialize raw models containing identity, metadata, payloads, errors or credentials.

### MR-V2-0404 — Financial policy changes
Financial rules must not be changed silently or activated by a generic admin form. Approval workflow and owner-approved Ground Truth are mandatory.

## 8. Platform requirements

### MR-V2-0501 — White-label
Branding, theme, domain and tenant-specific configuration must be separated from financial logic. Tenant architecture must not be invented from UI needs.

### MR-V2-0502 — Tenant safety
Company, tenant and branch isolation require an accepted architecture contract before broad write operations are enabled.

### MR-V2-0503 — PWA and platforms
PWA may provide installable shell and safe offline states, but financial API responses and sensitive operations must not be cached or executed offline. Native Android, iOS and Windows status must be reported honestly.

### MR-V2-0504 — Monitoring
Monitoring must avoid sensitive payloads and provide trace identifiers, health evidence, queue/outbox visibility and actionable failure information.

### MR-V2-0505 — Backup and restore
Backup existence is not enough. Restore drills, applied migration state and environment-specific evidence must be recorded.

## 9. Delivery and evidence requirements

### MR-V2-0601 — No guessing
Unknown financial or Kimia behavior must be classified as `BLOCKED BY GROUND TRUTH`, `CONFLICTED` or `NEEDS DECISION`.

### MR-V2-0602 — No reinventing
Before creating any service, controller, model, migration, repository, DTO, event, permission, route, API, schema, component, test or ADR, similar artifacts must be searched across canonical and historical evidence.

### MR-V2-0603 — No silent changes
Financial, architectural, migration, API, permission and customer-experience changes require documentation and traceability.

### MR-V2-0604 — Preserve first
No force push, destructive reset, shared-history rebase, broad revert, blind cherry-pick, branch deletion or applied migration rewrite without explicit owner instruction.

### MR-V2-0605 — Test honesty
Allowed test states:
- WRITTEN — NOT EXECUTED
- EXECUTED — PASS
- EXECUTED — FAIL
- NOT APPLICABLE

### MR-V2-0606 — Capability honesty
Allowed capability states:
- REUSE AS-IS
- REUSE AFTER FIX
- REFACTOR
- REBUILD
- IMPLEMENTED — NOT TESTED
- TESTED — NOT MERGED
- MERGED — CLOSURE PENDING
- BLOCKED BY GROUND TRUTH
- DUPLICATE CANDIDATE
- SUPERSEDED
- HISTORICAL ONLY
- NOT IMPLEMENTED

### MR-V2-0607 — Definition of Done
A capability is not complete until its requirement, source, business rules, architecture, database, backend, API, OpenAPI, frontend, permissions, audit, idempotency, tests, exact-SHA CI, PR/merge state, visual verification, documentation and remaining risk are traceable.

## 10. Current V2-00 limitations

The following remain evidence gaps and are not silently resolved by this document:
- Complete repository branch inventory
- Complete historical PR ledger
- Applied production migration state
- Database export or snapshot
- Full Kimia Write payload Ground Truth
- Pricing, commission, freeze and credit rules
- Confirmed tenant/company/branch architecture
- Real production deployment and restore evidence
- Full visual verification on supported devices

This document is a baseline requirement registry, not a claim that all requirements are implemented.