# GoldPlatform V2 — Project State

- Updated: 2026-08-06
- Owner: Alireza Bahramian
- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Status: `IN PROGRESS — READINESS EVIDENCE CONSOLIDATION`
- Evidence branch: `recovery/rc2-product-rebuild`
- Evidence Head at start: `cd92a1144bdfbe043bae1871aab9d623ce8bad64`
- V2 working branch: `v2/source-recovery-v2-00`
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

## Shared Kimia conversation recovery result

- Historical source preserved as `SRC-V2-0006`.
- 45 material claims extracted and classified.
- Evidence levels, conflicts, final status and V2 action recorded.
- Lower-priority chat conclusions corrected where higher-priority Runtime Ground Truth existed.
- Capability Matrix and Implementation Audit now reference the Claim Registry directly.
- Chat-generated Adapter classes and guessed Action defaults remain non-implementation evidence.
- Coin/Currency transaction-sum balance derivation, internal Wallet authority and unavailable-as-zero behavior are rejected or superseded.

## Recovered Kimia result

Historical confusion between operation/form codes and API Actions has been classified:

- Operational/form terminology may use `3 = buy` and `4 = sell`.
- Swagger/API Actions use `32 = buy` and `64 = sell`.
- Runtime evidence recorded for AccountId `350` maps customer buy to Kimia sell/API Action `64` and customer sell to Kimia buy/API Action `32`.
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

- `e67b109df29188a1a0762681b8feb7394ab4d5bd` — Backend RC1 Validation #346 — `EXECUTED — PASS`
- `d86df86ab5ea2bd8639ced0d3087b0acf3575d14` — Backend RC1 Validation #347 — `EXECUTED — PASS`
- `795483794f024e03c7f52cd11123fa29150e4adc` — Backend RC1 Validation #350 — `EXECUTED — PASS`
- `9159392c9461bd3de3a9aa8aea15e8535759d060` — Backend RC1 Validation #351 — `EXECUTED — PASS`
- `23ac8e32e97e3187875fda6309636b0dbf187027` — Backend RC1 Validation #352 — `EXECUTED — PASS`

Later documentation commits created newer Heads and require their own exact-SHA CI verification before closure.

## Current Gate status

- Mandatory V2 documents: `PASS`
- Shared Kimia conversation Claim Registry: `IMPLEMENTED`
- Claim correction against higher-priority Ground Truth: `IMPLEMENTED`
- Capability/Implementation traceability connection: `IMPLEMENTED — CI PENDING ON NEWEST HEAD`
- PR merged: `NO`
- Complete branch inventory: `INCOMPLETE`
- Complete all-PR evidence ledger: `INCOMPLETE`
- Database applied-migration/export evidence: `INCOMPLETE`
- Real-device visual verification: `INCOMPLETE`
- Production-environment evidence: `INCOMPLETE`

## Remaining V2-00 blockers

1. Complete branch inventory and exact Head SHA ledger.
2. Complete PR ledger across Open, Draft, Closed and Merged history.
3. Connect remaining capability records to exact PR, SHA and CI evidence.
4. Recover broader sanitized Kimia request/response evidence without enabling Write.
5. Recover database/export/applied-migration evidence when available.
6. Record real visual verification separately from static demo evidence.
7. Verify CI on the newest documentation Head.

## Honest readiness decision

`V2-00` is not yet complete. Documentation and one major conversation-recovery slice are implemented, but repository-history, complete capability traceability and external-environment evidence remain incomplete. `V2-01` must not begin until the readiness gate is explicitly re-evaluated and passed.
