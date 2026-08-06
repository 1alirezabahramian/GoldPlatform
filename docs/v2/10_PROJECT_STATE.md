# GoldPlatform V2 — Project State

- Updated: 2026-08-06
- Owner: Alireza Bahramian
- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Status: `IN PROGRESS — GATE NOT PASSED`
- Evidence branch: `recovery/rc2-product-rebuild`
- Evidence Head at start: `cd92a1144bdfbe043bae1871aab9d623ce8bad64`
- V2 working branch: `v2/source-recovery-v2-00`
- Pull request: `#195` — Open, Draft, not merged
- Production Ready: `NOT CLAIMED`

## Current documentation inventory

The current V2-00 branch contains these evidence documents:

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
14. `docs/v2/13_RULE_CAPABILITY_EVIDENCE_AUDIT.md`
15. `docs/v2/13_CHAT_CLAIM_REGISTRY_SHARED_KIMIA_CONVERSATION.md`
16. `docs/v2/14_V2_00_RESTART_BASELINE.md`
17. `docs/v2/14_CHAT_CLAIM_REGISTRY_CORRECTIONS.md`
18. `docs/v2/15_SOURCE_RECOVERY_SLICE_01.md`
19. `docs/v2/16_DOCUMENT_NAMESPACE_AUDIT.md`
20. `docs/v2/17_V2_00_EVIDENCE_GATE_AUDIT.md`

Numeric prefixes `13` and `14` are each duplicated. The files are distinct in content. Classification: `DUPLICATE NAMESPACE CANDIDATE`. No rename, deletion, move or renumber has been performed.

## Verified architecture state

- Kimia is the final authority for customer Money, Gold, Coin and Currency balances.
- GoldPlatform is the final authority for physical Custody/Amanat.
- Ledger, Journal, Event, Idempotency and Projection artifacts are audit, workflow and reconciliation evidence only.
- Customer financial data fails closed when verified Kimia account resolution is unavailable.
- Frontend performs no financial calculation, Weight750 calculation or Rial/Toman conversion.
- Kimia Read and Kimia Write remain separate capabilities.
- Kimia Write remains deny-by-default until exact request/response, idempotency, readback and reconciliation Ground Truth exists.
- Static demos remain `SUPERSEDED — TECHNICAL PREVIEW ONLY — NOT PRODUCT EVIDENCE`.

## Shared Kimia conversation recovery result

- Historical source preserved as `SRC-V2-0006`.
- 45 material claims extracted and classified in `CR-CHAT-KIMIA-0001`.
- Evidence levels, conflicts, final status and V2 action recorded.
- Lower-priority chat conclusions corrected through a linked correction ledger where higher-priority runtime evidence existed.
- Claim results are integrated into Source Index, Business Rule Registry, Kimia Ground Truth, Capability Matrix, Implementation Audit, Gap/Drift, Decision Log, Chat Audit and Project State.
- Chat-generated adapters and guessed Action defaults remain non-implementation evidence.
- Coin/Currency transaction-sum balance derivation, internal Wallet authority and unavailable-as-zero behavior are rejected or superseded.

## Recovered Kimia result

- Operational/form terminology may use `3 = buy` and `4 = sell`.
- Swagger/API Actions use `32 = buy` and `64 = sell`.
- Runtime evidence recorded for AccountId `350` maps customer buy to Kimia sell/API Action `64` and customer sell to Kimia buy/API Action `32`.
- This classification is limited to the recorded mapping and does not authorize production Kimia Write or prove every endpoint/product/environment.

## Decisions separated from continuing recovery

The following remain pending but do not stop evidence recovery:

- Tenant/Company/Branch architecture.
- Approved Kimia write gateway and endpoint-specific payload/result semantics.
- Pricing/commission/freeze/credit registry closure.
- Canonical ADR, Project State and V2 document namespace normalization.
- Customer registration ↔ Kimia account lifecycle and default group policy.
- Custody conversion/resale semantics.
- Approved sanitized real Kimia evidence set.

These block only dependent implementation or activation. The owner is not asked to repeat rules while existing evidence remains unexhausted.

## Safety result

- Feature code: unchanged
- Financial implementation/rules: unchanged; documentation classification only
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
- `68a36cea210508991ffedf715e0d91c35edbf91b` — Backend RC1 Validation #355 — `EXECUTED — PASS`
- `09175f7cacb43025beb318adec68ed6e2f92b56a` — Backend RC1 Validation #358 — `EXECUTED — PASS`
- `8fa2e294c02683c1cd329302b2ac04f8a067b02a` — Backend RC1 Validation #359 — `EXECUTED — PASS`
- `03a0bd091a10328f391eb8fad8ef6e32bcf9e1f5` — Backend RC1 Validation #360 — `EXECUTED — PASS`

Later documentation commits create newer Heads and require their own exact-SHA CI before any final gate claim.

## Current Gate status

Reference: `docs/v2/17_V2_00_EVIDENCE_GATE_AUDIT.md`

### Passed

- Mandatory documentation baseline exists.
- Shared Kimia conversation Claim Registry and correction path exist.
- Architecture boundaries are documented.
- Documentation-only safety boundary is preserved.
- Exact-SHA CI passed through Run #360.
- Namespace drift is explicitly identified and preserved.

### Not passed

- Exact CI on the final V2-00 Head.
- Canonical/unambiguous document namespace or formal carry-forward decision.
- Complete Branch → Head SHA ledger.
- Complete PR → Base/Head/Merge SHA → CI ledger.
- Complete Capability → Rule → File → PR → SHA → CI closure.
- Broader sanitized Kimia evidence.
- Database applied-migration/export evidence.
- Real frontend visual verification.
- Production environment, restore and monitoring evidence.

## Honest readiness decision

`V2-00 — GATE NOT PASSED — CONTINUE STRICT EVIDENCE RECOVERY`

`V2-01` remains `NOT STARTED`. Production Ready remains `NOT CLAIMED`.
