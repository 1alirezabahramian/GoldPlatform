# GoldPlatform V2 — Source Index

- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Owner: Alireza Bahramian
- Created: 2026-08-06
- Canonical evidence branch at start: `recovery/rc2-product-rebuild`
- Canonical evidence SHA at start: `cd92a1144bdfbe043bae1871aab9d623ce8bad64`
- V2 working branch: `v2/source-recovery-v2-00`
- Status: `IN PROGRESS — AUDIT ONLY`

## Safety boundary

This stage creates documentation only. It does not add a feature, migration, financial rule, Kimia Write mapping, balance mutation, branch deletion, history rewrite, rebase, force push, or destructive integration.

## Source priority

1. Real Kimia API output
2. Official Kimia Swagger/OpenAPI
3. Accepted Project Memory and ADRs
4. Owner-confirmed rules and examples
5. Canonical GitHub code and exact-SHA CI evidence
6. Historical chats, ZIPs, demos, and closed-unmerged PRs as evidence only

## Sources confirmed available

| ID | Source | Location | Classification | Initial note |
|---|---|---|---|---|
| SRC-V2-0001 | Canonical repository | `1alirezabahramian/GoldPlatform` | CURRENT CODE EVIDENCE | Default branch is `main`; V2 evidence base is Recovery branch. |
| SRC-V2-0002 | Recovery canonical branch | `recovery/rc2-product-rebuild` | CURRENT DEVELOPMENT TRUTH | Head verified at `cd92a114...`; latest commit merges PR #194. |
| SRC-V2-0003 | Project Memory | `docs/00_PROJECT_MEMORY.md` and uploaded copy | ACCEPTED LIVING GROUND TRUTH | Contains architecture, financial boundaries, Kimia terminology, rules and historical state. |
| SRC-V2-0004 | Domain Workshop | uploaded `41_GOLDPLATFORM_DOMAIN_WORKSHOP_2026-07-28.md` | OWNER-ACCEPTED DOMAIN EVIDENCE | Accepted working contract; Alireza is domain expert and financial-rule authority. |
| SRC-V2-0005 | Kimia Integration Audit | `docs/08_KIMIA_INTEGRATION_AUDIT.md` and uploaded copy | TECHNICAL AUDIT | Draft based on Swagger, runtime logs and repository review; not proof that implementation is complete. |
| SRC-V2-0006 | Shared Kimia conversation export | uploaded `gp.txt`; shared URL; `docs/v2/13_CHAT_CLAIM_REGISTRY_SHARED_KIMIA_CONVERSATION.md` | HISTORICAL CONVERSATION EVIDENCE — CLAIMS CLASSIFIED | Claims are now extracted with Evidence Level, Conflict, final status and V2 action; chat text is not canonical ground truth. |
| SRC-V2-0007 | Customer OpenAPI | `docs/api/customer-v1.openapi.yaml` | CURRENT CONTRACT EVIDENCE | Must be compared with live routes, resources, tests and frontend clients. |
| SRC-V2-0008 | Project state files | `docs/PROJECT_STATE.md`, `docs/project_state.md` | DRIFT CANDIDATE | Two case-different state files exist and require comparison/classification. |
| SRC-V2-0009 | ADR directories | `docs/ADR/`, `docs/adr/` | DRIFT / DUPLICATE-NAMESPACE CANDIDATE | Case-different ADR trees and duplicate ADR numbers exist; preserve and inventory before normalization. |
| SRC-V2-0010 | Recovery documentation | `docs/recovery/` | CURRENT RECOVERY EVIDENCE | Contains boundary guards, closure checkpoints, frontend recovery and operationalization evidence. |
| SRC-V2-0011 | GitHub PR history | PRs Open/Draft/Closed/Merged | DEVELOPMENT HISTORY | Closed-not-merged work is historical only; merged alone is not completion proof. |
| SRC-V2-0012 | GitHub Actions workflows | `.github/workflows/` | TEST/CI CONTRACT | Exact-SHA execution must be recorded separately from workflow existence. |
| SRC-V2-0013 | Static demo | `demo-preview/` and PRs #191–#194 | SUPERSEDED — TECHNICAL PREVIEW ONLY — NOT PRODUCT EVIDENCE | Fictional, disconnected and intentionally non-operational. |
| SRC-V2-0014 | Backend code/tests | `backend/` | CURRENT IMPLEMENTATION EVIDENCE | Includes Kimia read boundary, financial guards, customer resources, operator/admin endpoints, tests and migrations. |
| SRC-V2-0015 | Frontends | canonical Customer and Admin/Operator packages | CURRENT IMPLEMENTATION EVIDENCE | Must be mapped to exact backend contracts and visual verification status. |

## GitHub findings — first pass

- Repository access: admin/push confirmed through the connected GitHub app.
- Default branch: `main`.
- Reference branch exists: `recovery/rc2-product-rebuild`.
- Reference Head: `cd92a1144bdfbe043bae1871aab9d623ce8bad64`.
- Latest reference commit: merge of PR #194, a fictional premium Customer demo.
- Comparing Stage-00 merge `1c8555c5...` to the reference branch reports a diverged history: 593 commits ahead and 11 behind, with merge base `0d618bf6...`. This forbids treating `main` and Recovery as interchangeable.
- No pull-request-triggered workflow runs were returned for the reference merge SHA by the connector. Therefore CI on the exact merge SHA is currently `NOT CONFIRMED`, not PASS or FAIL.
- Recent history includes both canonical merged recovery work and many preserved closed-not-merged stacks (notably AP/OP/older reconstruction PRs).

## Required sources not yet recovered

| Source | Current state | Next recovery action |
|---|---|---|
| Full branch inventory with Head SHA | PARTIAL | Page through all branches and classify canonical, merged-history, evidence-only and duplicate candidates. |
| Complete PR inventory before PR #90 | PARTIAL | Continue paginated PR recovery and build ordered PR ledger. |
| Real Kimia raw responses | NOT YET VERIFIED IN V2 | Locate sanitized stored outputs/log evidence; do not call Write endpoints. |
| Official Kimia Swagger exact current file | PARTIAL | Locate repository Swagger/OpenAPI and compare to uploaded audit and real-output evidence. |
| ZIP archives / database exports | NOT YET LOCATED | Search uploaded files, repository history and available library evidence. |
| Previous conversation artifacts | PARTIAL — ONE SHARED KIMIA CHAT CLASSIFIED | Continue converting each recovered conversation into a Claim Registry rather than copying chat text as truth. |
| Exact CI per capability SHA | NOT YET COMPLETE | Correlate PR Head, merge SHA, workflow runs and check status. |

## Known initial drift/duplicate candidates

1. `docs/PROJECT_STATE.md` vs `docs/project_state.md`.
2. `docs/ADR/` vs `docs/adr/`.
3. Duplicate ADR identifiers such as ADR-027 through ADR-030 with different subjects.
4. Multiple historical Kimia clients/services were removed or replaced; canonical path must be identified per current SHA.
5. Static demo work is merged but remains non-product evidence.
6. Older AP/OP stacks are closed-not-merged and cannot be considered canonical, even where later code reused selected patterns.
7. Shared Kimia conversation contained conflicting Action mappings, schema examples presented as real outputs, Wallet authority drift and unsupported Coin/Currency balance derivation; these are now explicitly classified in `docs/v2/13_CHAT_CLAIM_REGISTRY_SHARED_KIMIA_CONVERSATION.md`.

## Claim-registry integration

Historical conversations must be integrated using this structure:

`Claim → Source → Evidence Level → Conflict/Unknown → Final Classification → V2 Action`

The first completed registry is:

- `CR-CHAT-KIMIA-0001`
- File: `docs/v2/13_CHAT_CLAIM_REGISTRY_SHARED_KIMIA_CONVERSATION.md`
- Result: 45 material claims classified; unresolved financial/Kimia mappings remain blocked; unsupported and superseded claims are explicitly rejected.

## Evidence handling rule

Nothing is marked “missing”, “complete”, “production ready”, or “confirmed by Kimia” until the relevant source, implementation, test execution, exact-SHA CI, merge state and visual/operational verification have been linked.
