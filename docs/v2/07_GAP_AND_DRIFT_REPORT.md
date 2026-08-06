# GoldPlatform V2 — Gap and Drift Report

**Stage:** V2-00  
**Status:** IN PROGRESS

## Critical architecture drift

### Financial authority drift

Historical code and documentation include internal Wallet, Ledger and Balance Projection behavior that was at times described or used as customer balance authority. Current accepted architecture requires Kimia to remain final authority for Money, Gold, Coin and Currency.

**Classification:** REUSE AFTER FIX / HISTORICAL ONLY depending on path.  
**Required action:** preserve records and audit capability; prevent customer serialization, sufficiency decisions and settlement completion from relying on internal projections.

### Registration drift

Historical registration created local RIAL and GOLD18 wallet accounts before the accepted Kimia-account resolution was complete. Current Auth and registration contracts also contain incomplete or legacy behavior.

**Gap:** accepted OTP-only registration, verified Tenant ownership, privacy-safe errors/logging and customer-to-Kimia Account mapping are not closed.

### Trading and write drift

Historical prototypes accepted client identity, price or commission and used local Trade/Ledger flows without verified Kimia write completion. Recovery quarantined public prototype routes and added fail-closed boundaries.

**Gap:** no approved end-to-end Kimia Write contract exists.

## Repository structure drift

- `docs/PROJECT_STATE.md` and `docs/project_state.md` coexist.
- `docs/ADR/` and `docs/adr/` coexist.
- ADR numbers 027–030 are reused for different decisions.
- Multiple historical Kimia clients, repositories and services existed; some were removed, others preserved for review.
- Historical AP/OP stacked PRs contain useful evidence but many are Closed — Not Merged.
- Demo implementations exist alongside executable Frontend and must not be treated as product evidence.

No path is deleted or renamed during V2-00. A canonical-document map must be approved before consolidation.

## Ground-truth gaps

| Gap | Classification | Risk |
|---|---|---|
| Kimia write payloads and endpoint semantics | BLOCKED BY GROUND TRUTH | Critical |
| Weight750 formula across all product types | BLOCKED BY GROUND TRUTH | Critical |
| Coin/currency final balance retrieval model | BLOCKED BY REAL OUTPUT | High |
| Customer-to-Kimia account mapping lifecycle | REUSE AFTER FIX | Critical |
| Commission, pricing, spread and rounding rules | NEEDS SOURCE RECOVERY | Critical |
| Freeze and anti-scalping rules | NEEDS SOURCE RECOVERY | High |
| Credit limits and negative-balance authorization | NEEDS SOURCE RECOVERY | Critical |
| Settlement and reconciliation completion proof | BLOCKED BY GROUND TRUTH | Critical |
| Tenant/Company/Branch architecture | NEEDS OWNER DECISION AFTER EVIDENCE | Critical |
| Physical inventory and custody linkage | REUSE AFTER FIX | High |

## Capability gaps

### Authentication and KYC

OTP verification/logout and safe registration were historically incomplete. KYC is not proven complete. Public API exceptions require explicit allowlists, neutral errors, privacy-safe logs and rate limits.

### Customer financial dashboard

Real balance cards remain blocked until authenticated customer-to-Kimia account resolution is verified. Unavailable financial data must remain unavailable rather than zero or internal Wallet values.

### Operator and Admin

Read-only foundations and selected delivery actions exist in canonical recovery history. Broader historical AP capabilities must be compared against canonical code before reuse. Branch and Tenant scope remain unresolved.

### Native platforms

PWA foundation exists. Native Android, iOS and Windows packages are not implemented or visually verified on real devices.

### Production

Production Ready is not established. Environment deployment, secrets, TLS, monitoring, alert delivery, backup restoration and post-merge CI evidence remain environment-dependent.

## Test and CI drift

A written test is not an executed test. A green descendant run is not direct green CI for an earlier SHA. A green PR Head is not automatically a post-merge canonical SHA run.

V2 matrix must record separately:

- test file;
- WRITTEN — NOT EXECUTED / EXECUTED — PASS / EXECUTED — FAIL;
- exact tested SHA;
- workflow and run ID;
- PR Head and Merge SHA.

## Duplicate candidates

Potential duplicate areas requiring comparison before any new implementation:

- Kimia clients and repositories
- Customer balance presenters/read models
- Admin/Operator dashboards and queues
- Customer Frontend foundations
- Permission catalogs
- ADRs with reused numbers
- Project State documents
- Demo shells and executable Frontend shells

## Immediate safe actions

1. Complete source/PR/branch inventory.
2. Build canonical document and ADR map.
3. Expand Rule Registry from accepted owner and Kimia evidence.
4. Link capabilities to current canonical files and historical PR evidence.
5. Record exact CI evidence without changing runtime behavior.

## Prohibited shortcuts

- declaring a capability absent after one search;
- merging historical stacks to recover features;
- converting internal projection values into customer balances;
- enabling Kimia Write from Swagger examples alone;
- creating Tenant or Branch entities without accepted architecture;
- treating demo pages as visual verification of the real product.
