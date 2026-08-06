# GoldPlatform V2 — Project State

- Updated: 2026-08-06
- Owner: Alireza Bahramian
- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Status: `IN PROGRESS — READINESS EVIDENCE CONSOLIDATION`
- Evidence branch: `recovery/rc2-product-rebuild`
- Evidence Head at start: `cd92a1144bdfbe043bae1871aab9d623ce8bad64`
- V2 working branch: `v2/source-recovery-v2-00`
- Verified V2 Head before this state update: `497e0fd7ba87e5a7c3a5593642f76d928a41bedb`
- Pull request: `#195` — Open, Draft, not merged
- Production Ready: `NOT CLAIMED`

## Completed recovery outputs

All eleven mandatory V2-00 documents now exist:

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
- Runtime evidence recorded for AccountId 350 maps customer buy to Kimia sell/API Action 64 and customer sell to Kimia buy/API Action 32.
- This evidence does not authorize production Kimia Write.

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

- SHA: `226acad55620c721d563f81c687b37b6e1b0a47f`
  - Workflow: Backend RC1 Validation
  - Run: `#331`
  - Status: `EXECUTED — PASS`
- SHA: `6d5bc28e6381d2a947bf1ee0c534259a26c72be4`
  - Workflow: Backend RC1 Validation
  - Run: `#335`
  - Status: `EXECUTED — PASS`
- SHA: `497e0fd7ba87e5a7c3a5593642f76d928a41bedb`
  - Workflow: Backend RC1 Validation
  - Run: `#338`
  - Status: `EXECUTED — PASS`

This state update creates a newer Head and therefore requires a fresh exact-SHA CI result before closure.

## Current Gate status

- Mandatory V2 documents: `PASS`
- Exact-SHA CI for previous Head: `PASS`
- Exact-SHA CI for current state-update Head: `PENDING`
- PR merged: `NO`
- Complete branch inventory: `INCOMPLETE`
- Complete all-PR evidence ledger: `INCOMPLETE`
- Database applied-migration/export evidence: `INCOMPLETE`
- Real-device visual verification: `INCOMPLETE`
- Production-environment evidence: `INCOMPLETE`

## Remaining V2-00 blockers

1. Complete branch inventory and exact Head SHA ledger.
2. Complete PR ledger across Open, Draft, Closed and Merged history.
3. Connect capability records to exact PR, SHA and CI evidence where available.
4. Recover database/export/applied-migration evidence when available.
5. Record real visual verification separately from static demo evidence.
6. Re-run CI on the new Head created by this state update.

## Honest readiness decision

`V2-00` is not yet complete. Documentation creation is complete, but repository-history and external-environment evidence remain incomplete. `V2-01` must not begin until the readiness gate is explicitly re-evaluated and passed.