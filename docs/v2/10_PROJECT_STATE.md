# GoldPlatform V2 — Project State

- Updated: 2026-08-06
- Owner: Alireza Bahramian
- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Status: `IN PROGRESS — READINESS EVIDENCE CONSOLIDATION`
- Evidence branch: `recovery/rc2-product-rebuild`
- Evidence Head at start: `cd92a1144bdfbe043bae1871aab9d623ce8bad64`
- V2 working branch: `v2/source-recovery-v2-00`
- Verified V2 Head before this state update: `795483794f024e03c7f52cd11123fa29150e4adc`
- Pull request: `#195` — Open, Draft, not merged
- Production Ready: `NOT CLAIMED`

## Completed recovery outputs

The V2-00 documentation set currently includes:

1. `docs/v2/00_SOURCE_INDEX.md`
2. `docs/v2/01_MASTER_REQUIREMENTS.md`
3. `docs/v2/02_BUSINESS_RULE_REGISTRY.md`
4. `docs/v2/03_KIMIA_GROUND_TRUTH.md`
5. `docs/v2/04_CAPABILITY_TRACEABILITY_MATRIX.md`
6. `docs/v2/05_ARCHITECTURE_CONTRACT.md`
7. `docs/v2/06_IMPLEMENTATION_AUDIT.md`
8. `docs/v2/07_GAP_AND_DRIFT_REPORT.md`
9. `docs/v2/08_V2_ROADMAP.md`
10. `docs/v2/09_DECISION_LOG.md`
11. `docs/v2/10_PROJECT_STATE.md`
12. `docs/v2/11_REPOSITORY_EVIDENCE_LEDGER.md`
13. `docs/v2/12_CHAT_EXECUTION_AUDIT.md`
14. `docs/v2/13_CHAT_CLAIM_REGISTRY_SHARED_KIMIA_CONVERSATION.md`
15. `docs/v2/14_CHAT_CLAIM_REGISTRY_CORRECTIONS.md`

## Verified architecture state

- Kimia is the final authority for customer Money, Gold, Coin and Currency balances.
- GoldPlatform is the final authority for physical Custody/Amanat.
- Ledger, Journal, Event, Idempotency and Projection artifacts are audit, workflow and reconciliation evidence only.
- Customer financial data must fail closed when verified Kimia account resolution is unavailable.
- Frontend must not calculate financial values, Weight750 or Rial/Toman conversions.
- Kimia Read and Kimia Write remain separate boundaries.
- Kimia Write remains disabled until exact request/response and reconciliation Ground Truth is approved.
- Static demos remain `SUPERSEDED — TECHNICAL PREVIEW ONLY — NOT PRODUCT EVIDENCE`.

## Recovered Kimia result

Historical confusion between operation/form codes and API Actions has been classified:

- Operational/form terminology may use `3 = buy` and `4 = sell`.
- Swagger/API Actions use `32 = buy` and `64 = sell`.
- Runtime evidence recorded for AccountId `350` maps customer buy to Kimia sell/API Action `64` and customer sell to Kimia buy/API Action `32`.
- Money/Product code `4` is accepted only inside the specific recovered mapping; it is not a universal hard-coded identifier contract.
- This evidence does not authorize production Kimia Write.

## Shared conversation recovery result

The shared Kimia conversation was not copied into V2 as truth. It was converted into a Claim Registry:

- 45 material claims extracted;
- evidence levels assigned;
- conflicts and unknowns recorded;
- accepted, blocked, rejected, historical and superseded claims separated;
- higher-priority runtime evidence applied through a separate correction record.

Important final outcomes:

- Coin/Currency balances must not be derived from transaction history as final balances.
- local Wallet must not become a competing customer balance authority.
- unavailable Kimia data must not be shown as zero.
- generated transaction-adapter examples are not implementation evidence.
- Kimia Write remains fail-closed.

## Safety result

- Feature code: unchanged
- Financial code/rules: unchanged
- Kimia Write: unchanged and disabled
- Migration/database: unchanged
- API/OpenAPI: unchanged
- Frontend behavior: unchanged
- Branch/PR/file deletion: none
- History rewrite/rebase/force push: none

## Exact-SHA CI evidence

- `226acad55620c721d563f81c687b37b6e1b0a47f` — Backend RC1 Validation `#331` — `EXECUTED — PASS`
- `6d5bc28e6381d2a947bf1ee0c534259a26c72be4` — Backend RC1 Validation `#335` — `EXECUTED — PASS`
- `497e0fd7ba87e5a7c3a5593642f76d928a41bedb` — Backend RC1 Validation `#338` — `EXECUTED — PASS`
- `e67b109df29188a1a0762681b8feb7394ab4d5bd` — Backend RC1 Validation `#346` — `EXECUTED — PASS`
- `d86df86ab5ea2bd8639ced0d3087b0acf3575d14` — Backend RC1 Validation `#347` — `EXECUTED — PASS`
- `795483794f024e03c7f52cd11123fa29150e4adc` — Backend RC1 Validation `#350` — `EXECUTED — PASS`

These runs validate their exact documentation SHAs only. They do not prove Kimia Write, financial formulas or production readiness.

This state update creates a newer Head and therefore requires a fresh exact-SHA CI result before any closure claim.

## Current Gate status

- Mandatory V2 documentation baseline: `PASS`
- Shared Kimia conversation Claim Registry: `IMPLEMENTED`
- Claim correction against higher-priority evidence: `IMPLEMENTED`
- Exact-SHA CI through `79548379...`: `EXECUTED — PASS`
- Exact-SHA CI for current state-update Head: `PENDING`
- PR merged: `NO`
- Complete branch inventory: `INCOMPLETE`
- Complete all-PR evidence ledger: `INCOMPLETE`
- Complete Capability → File → PR → SHA → CI closure: `INCOMPLETE`
- Database applied-migration/export evidence: `INCOMPLETE`
- Real-device visual verification: `INCOMPLETE`
- Production-environment evidence: `INCOMPLETE`

## Remaining V2-00 blockers

1. Complete branch inventory and exact Head SHA ledger.
2. Complete PR ledger across Open, Draft, Closed and Merged history.
3. Connect capability records to exact files, PRs, SHAs and CI evidence.
4. Continue converting recovered conversations and exports into Claim Registries.
5. Recover database/export/applied-migration evidence when available.
6. Record real visual verification separately from static demo evidence.
7. Resolve remaining Kimia Ground Truth only through approved sanitized real evidence; do not enable Write.
8. Obtain CI on the current Head created by this state update.

## Honest readiness decision

`V2-00` is not yet complete. Documentation recovery has materially advanced, but repository-history, capability traceability, database evidence, real-device verification and production evidence remain incomplete. `V2-01` must not begin until the readiness gate is explicitly re-evaluated and passed.
