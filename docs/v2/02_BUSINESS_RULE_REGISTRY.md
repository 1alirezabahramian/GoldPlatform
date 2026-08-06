# GoldPlatform V2 — Business Rule Registry

- Stage: `V2-00`
- Status: `INITIAL EXTRACTION — NOT COMPLETE`
- Rule IDs in this file are V2 traceability identifiers. They do not replace original BR/ADR identifiers.

## Status vocabulary

`ACCEPTED` · `CONFIRMED BY REAL KIMIA OUTPUT` · `CONFIRMED BY OWNER` · `DRAFT` · `HISTORICAL` · `SUPERSEDED` · `CONFLICTED` · `NEEDS DECISION` · `BLOCKED BY GROUND TRUTH`

## Initial registry

### BR-V2-0001 — Kimia is final authority for financial balances

- Text: Money, Gold, Coin and Currency customer balances are finally authoritative in Kimia. GoldPlatform must not create a competing final balance.
- Domain: Financial authority
- Source: Owner mission; Project Memory; Recovery PRs and boundary tests
- Status: `CONFIRMED BY OWNER`
- Backend: Customer financial reads must resolve to verified Kimia-backed data; internal projections are audit/reconciliation only.
- Frontend: Must not show internal Wallet/Ledger values as customer balances; unavailable Kimia data must not be replaced with zero.
- Admin/Operator: Internal projections must be labelled internal and non-authoritative.
- Audit/Idempotency: Required for intent/result and reconciliation, not balance authority.
- Tests: Architecture and HTTP boundary tests required.
- Related code evidence: Recovery PRs #153–#158 and #175; Customer source-state PR #186.
- Ambiguity: Real authenticated customer-to-Kimia account resolution remains a capability gap.

### BR-V2-0002 — GoldPlatform is final authority for physical Custody/Amanat

- Text: Physical custody is owned by GoldPlatform and must remain separate from financial balances.
- Domain: Custody
- Source: Owner mission; Project Memory; Domain Workshop
- Status: `CONFIRMED BY OWNER`
- Backend: Custody lifecycle, physical identity, delivery and branch handling remain local platform concerns.
- Frontend: Display Custody separately from Money/Gold/Coin/Currency.
- Admin/Operator: Physical workflow may be operated without implying Kimia financial balance mutation.
- Tests: Separation and ownership/authorization tests required.
- Related code evidence: `CustodyAsset`, `DeliveryRequest`, customer and operator custody/delivery resources.

### BR-V2-0003 — Internal financial records are evidence, not final balances

- Text: Ledger, Journal, Event Store, Idempotency Registry and Balance Projection serve audit, trace, workflow, intent/result and reconciliation.
- Status: `CONFIRMED BY OWNER`
- Backend: They cannot independently approve sufficiency or mark external settlement successful.
- Related evidence: PRs #153–#156, #158, #175; financial boundary tests.

### BR-V2-0004 — No floating-point arithmetic for money or weight

- Text: Money and weight use exact Decimal or decimal strings; float is prohibited.
- Domain: Financial precision
- Status: `CONFIRMED BY OWNER`
- Backend: Central exact-decimal utilities and validated scale/rounding rules.
- Frontend: Decimal values remain strings; no independent financial calculation.
- Related evidence: `App\Support\Decimal`, ADR-027 financial decimal contract, precision tests.
- Gap: Each domain field scale and rounding policy still requires full source tracing.

### BR-V2-0005 — Kimia Rial, platform Toman conversion is backend-only

- Text: Kimia operates in Rial while customer-facing platform values are in Toman. Conversion must be central, explicit and tested in Backend.
- Status: `CONFIRMED BY OWNER`
- Frontend: No Rial/Toman conversion.
- Risk: Existing contracts may intentionally preserve raw units where conversion ground truth was not implemented; capability audit must identify every boundary.

### BR-V2-0006 — Coin and Currency catalogs are dynamic

- Text: Coin and Currency identifiers must be read from Kimia and never hard-coded from examples.
- Status: `CONFIRMED BY OWNER`
- Kimia: `/api/product/coins`, `/api/product/currencies` are documented read sources.
- Backend: Local snapshots, if present, are rebuildable caches with sync timestamps.
- Tests: No sample identifier may become a production constant.

### BR-V2-0007 — Kimia Read and Write are separate capabilities

- Text: Read and Write clients/policies/retry behavior must be separated. Controllers do not call Kimia client directly.
- Status: `CONFIRMED BY OWNER`
- Backend: Application service/repository boundary required.
- Related evidence: PRs #150, #164–#165; HTTP and service Kimia boundary tests.

### BR-V2-0008 — Kimia Write is deny-by-default

- Text: No Kimia Write is enabled without real Ground Truth for endpoint, payload, codes, idempotency and result verification.
- Status: `CONFIRMED BY OWNER`
- Backend: Successful Write must be followed by balance readback and reconciliation evidence.
- Retry: Write retry policy differs from Read and must account for unknown outcome.
- Related evidence: `KimiaWritePreparationService`, deny-by-default ADR and tests.
- Current capability status: `BLOCKED BY GROUND TRUTH` until complete real-output mapping exists.

### BR-V2-0009 — Credentials and secrets are never stored in Git, UI, logs or docs

- Status: `ACCEPTED`
- Scope: Kimia credentials, API keys, tokens, passwords and private endpoints where sensitive.
- Tests: Secret scans and response/log redaction.

### BR-V2-0010 — Customer frontend is Persian, RTL, mobile-first and simple

- Text: Complexity remains in Backend; customer UI uses human language and never exposes internal accounting terms or Kimia identifiers.
- Status: `CONFIRMED BY OWNER`
- Frontend states: Loading, Empty, Error, Forbidden and Offline are required where applicable.
- Related evidence: Customer frontend recovery and UX PRs #170–#189.

### BR-V2-0011 — Admin, Operator and Customer are distinct experiences

- Status: `CONFIRMED BY OWNER`
- Backend: Permission authority remains server-side.
- Frontend: Navigation visibility is not authorization.
- Related evidence: Operator permission gates and three-role frontend/demo work.

### BR-V2-0012 — Demo is not product evidence

- Text: Previous HTML/static demos are `SUPERSEDED — TECHNICAL PREVIEW ONLY — NOT PRODUCT EVIDENCE`.
- Status: `CONFIRMED BY OWNER`
- Related evidence: PRs #191–#194 explicitly use fictional data and no operational connection.

### BR-V2-0013 — Negative balances are valid domain values

- Domain: Money/Gold/Coin/Currency
- Status: `CONFIRMED BY OWNER`
- Frontend: Negative must not be clamped to zero or treated as unavailable.
- Gap: Credit-limit/freeze/approval rules remain separately traceable and cannot be inferred from negative-balance support.

### BR-V2-0014 — Action codes must be endpoint-specific and source-confirmed

- Text: No global Action mapping may be inferred across Kimia endpoints.
- Source: Uploaded Kimia audit, Project Memory and conversation export show conflicting simplified codes versus Swagger bit-flag-style values.
- Status: `CONFLICTED`
- Exact conflict: Historical/owner-facing documents contain values such as 1/2/3/4/7/8, while reviewed Swagger descriptions include 2/4/32/64 and other powers of two depending on endpoint.
- Decision rule: Real Kimia output outranks Swagger; Swagger outranks internal historical notes.
- Required evidence: Sanitized real request/response and transaction records per operation.
- Backend: Kimia Write remains blocked; do not encode a default mapping from this conflict.

### BR-V2-0015 — Weight750 formula cannot be inferred

- Text: Frontend never calculates Weight750. Backend may only calculate it after exact formula, unit and rounding are confirmed.
- Status: `BLOCKED BY GROUND TRUTH`
- Conflict/risk: Historical text includes standard-industry reasoning, but that is not sufficient Kimia Ground Truth.

### BR-V2-0016 — Successful merge does not equal completion

- Status: `ACCEPTED`
- Required proof: code, rule, tests written/executed, exact-SHA CI, API/OpenAPI, permissions, audit, idempotency, visual verification and docs.

### BR-V2-0017 — Closed-not-merged PR is historical only

- Status: `ACCEPTED`
- Development effect: May provide patterns/evidence but is not canonical code.
- Initial examples: PRs #104–#148 include many closed-not-merged AP/OP/reconstruction branches; selected patterns were later reused in canonical recovery commits.

### BR-V2-0018 — Preserve history before integration

- Text: No force push, destructive reset, broad revert, shared-history rebase, blind cherry-pick or branch/PR deletion without explicit owner instruction.
- Status: `CONFIRMED BY OWNER`

## Conflicts requiring evidence, not immediate owner re-entry

1. Kimia Action codes and endpoint semantics.
2. Exact Weight750 formula, unit and rounding behavior.
3. Exact Kimia Write payloads and success/error/idempotency behavior.
4. Whether every historical commission, freeze, credit and anti-scalping rule remains current or was superseded.
5. Tenant/company/branch authority model.

These items remain blocked until the existing source corpus and real output evidence are exhausted. The owner is not asked to restate prior rules from memory.