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
| SRC-V2-0002 | Recovery canonical branch | `recovery/rc2-product-rebuild` | CURRENT DEVELOPMENT TRUTH | Base at V2 start is `cd92a114...`; later V2 work is documentation-only. |
| SRC-V2-0003 | Project Memory | `docs/00_PROJECT_MEMORY.md` and uploaded copy | ACCEPTED LIVING GROUND TRUTH | Architecture, financial boundaries, Kimia terminology, rules and historical state. |
| SRC-V2-0004 | Domain Workshop | uploaded `41_GOLDPLATFORM_DOMAIN_WORKSHOP_2026-07-28.md` | OWNER-ACCEPTED DOMAIN EVIDENCE | Accepted working contract; Alireza is domain expert and financial-rule authority. |
| SRC-V2-0005 | Kimia Integration Audit | `docs/08_KIMIA_INTEGRATION_AUDIT.md` and uploaded copy | TECHNICAL AUDIT | Draft based on Swagger, runtime logs and repository review; not implementation-completion proof. |
| SRC-V2-0006 | Shared Kimia conversation export | uploaded `gp.txt`; `docs/v2/13_CHAT_CLAIM_REGISTRY_SHARED_KIMIA_CONVERSATION.md`; correction `docs/v2/14_CHAT_CLAIM_REGISTRY_CORRECTIONS.md` | HISTORICAL CONVERSATION EVIDENCE — CLAIMS CLASSIFIED AND CORRECTED | 45 material claims classified and corrected through higher-priority evidence. |
| SRC-V2-0007 | Customer OpenAPI | `docs/api/customer-v1.openapi.yaml` | CURRENT CONTRACT EVIDENCE | Must be compared with routes, resources, tests and frontend clients. |
| SRC-V2-0008 | Project state files | `docs/PROJECT_STATE.md`, `docs/project_state.md` | DRIFT CANDIDATE | Case-different state files require classification. |
| SRC-V2-0009 | ADR directories | `docs/ADR/`, `docs/adr/` | DRIFT / DUPLICATE-NAMESPACE CANDIDATE | Preserve and inventory before normalization. |
| SRC-V2-0010 | Recovery documentation | `docs/recovery/` | CURRENT RECOVERY EVIDENCE | Boundary guards, closure checkpoints, frontend recovery and operationalization evidence. |
| SRC-V2-0011 | GitHub PR history | PRs Open/Draft/Closed/Merged | DEVELOPMENT HISTORY | Closed-not-merged is historical only; merged alone is not completion proof. |
| SRC-V2-0012 | GitHub Actions workflows | `.github/workflows/` | TEST/CI CONTRACT | Exact-SHA execution is recorded separately from workflow existence. |
| SRC-V2-0013 | Static demo | `demo-preview/` and PRs #191–#194 | SUPERSEDED — TECHNICAL PREVIEW ONLY — NOT PRODUCT EVIDENCE | Fictional and disconnected. |
| SRC-V2-0014 | Backend code/tests | `backend/` | CURRENT IMPLEMENTATION EVIDENCE | Requires exact capability/file/PR/SHA/CI mapping. |
| SRC-V2-0015 | Frontends | canonical Customer and Admin/Operator packages | CURRENT IMPLEMENTATION EVIDENCE | Requires backend-contract and visual-verification mapping. |
| SRC-V2-0016 | Document namespace audit | `docs/v2/16_DOCUMENT_NAMESPACE_AUDIT.md` | CURRENT V2-00 EVIDENCE | Duplicate numeric prefixes `13` and `14` confirmed; no destructive normalization performed. |
| SRC-V2-0017 | V2-00 evidence gate | `docs/v2/17_V2_00_EVIDENCE_GATE_AUDIT.md` | CURRENT STAGE-GATE EVIDENCE | Gate remains not passed. |
| SRC-V2-0018 | Current V2 PR evidence slice | `docs/v2/18_REPOSITORY_EVIDENCE_SLICE_02.md` | CURRENT GITHUB EVIDENCE | Base/Head/PR comparison and exact-SHA CI recorded. |
| SRC-V2-0019 | Key Recovery PR metadata slice | `docs/v2/19_RECOVERY_PR_EVIDENCE_SLICE_03.md` | CURRENT GITHUB EVIDENCE | Base SHA, Head SHA, Merge SHA, state and scope for PRs #149, #150, #153, #175 and #186. |
| SRC-V2-0020 | Key Recovery PR exact-Head CI slice | `docs/v2/20_RECOVERY_PR_CI_MAPPING_SLICE_04.md` | CURRENT GITHUB CI EVIDENCE | Exact Head workflows mapped and passed for PRs #149, #150, #153, #175 and #186. |

## Current GitHub findings

- Repository access through connected GitHub app is active.
- Default branch: `main`.
- V2 evidence Base: `recovery/rc2-product-rebuild` at `cd92a1144bdfbe043bae1871aab9d623ce8bad64` at stage start.
- PR #195 remains Open, Draft and Not Merged.
- Inspected V2 changes are documentation-only under `docs/v2/`.
- Five key merged Recovery PRs now have exact metadata and exact-Head CI mappings.
- Current canonical code equivalence for those PRs remains a separate uncompleted check.

## Required sources not yet recovered

| Source | Current state | Next recovery action |
|---|---|---|
| Full branch inventory with Head SHA | PARTIAL | Continue exact Head recovery in bounded families. |
| Complete PR inventory with all SHA/CI fields | PARTIAL | Continue metadata and exact-Head CI slices beyond the five verified PRs. |
| Real Kimia raw responses | PARTIAL — SANITIZED TRANSACTION EVIDENCE RECORDED | Continue locating sanitized request/response evidence; do not call Write endpoints. |
| Official Kimia Swagger exact current file | PARTIAL | Locate and hash exact Swagger/OpenAPI source. |
| ZIP archives / database exports | NOT YET LOCATED | Search repository history and File Library evidence. |
| Previous conversation artifacts | PARTIAL — ONE SHARED KIMIA CHAT CLASSIFIED | Continue Claim Registry conversion. |
| Current canonical code equivalence for mapped PRs | NOT YET COMPLETE | Verify current Base contains the intended guards/capabilities without superseding drift. |
| Exact CI per remaining capability SHA | NOT YET COMPLETE | Correlate PR Head, merge SHA, workflows and current code. |

## Known drift and duplicate candidates

1. `docs/PROJECT_STATE.md` vs `docs/project_state.md`.
2. `docs/ADR/` vs `docs/adr/`.
3. Duplicate ADR identifiers with different subjects.
4. Duplicate V2 document prefixes `13` and `14`.
5. Historical Kimia clients/services removed or replaced; canonical path must be identified per current SHA.
6. Static demo work is merged but non-product evidence.
7. Older AP/OP stacks are closed-not-merged and non-canonical unless independently reconstructed.
8. Shared Kimia conversation contains unsupported or superseded claims preserved through Registry and correction records.

## Evidence handling rule

Nothing is marked missing, complete, Production Ready, or confirmed by Kimia until the relevant source, implementation, test execution, exact-SHA CI, merge state and visual/operational verification are linked.

Historical PR CI proves only that the exact historical Head passed the named workflows. It does not by itself prove current canonical code equivalence or capability closure.
