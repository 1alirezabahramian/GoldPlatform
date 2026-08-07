# GoldPlatform V2 — Project State

- Updated: 2026-08-06
- Owner: Alireza Bahramian
- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Status: `IN PROGRESS — STRICT EVIDENCE RECOVERY`
- Evidence branch: `recovery/rc2-product-rebuild`
- Evidence Head at start: `cd92a1144bdfbe043bae1871aab9d623ce8bad64`
- V2 working branch: `v2/source-recovery-v2-00`
- Pull request: `#195` — Open, Draft, not merged
- Production Ready: `NOT CLAIMED`

## Current evidence set

The V2-00 evidence set includes the baseline documents plus:

- `docs/v2/13_RULE_CAPABILITY_EVIDENCE_AUDIT.md`
- `docs/v2/13_CHAT_CLAIM_REGISTRY_SHARED_KIMIA_CONVERSATION.md`
- `docs/v2/14_V2_00_RESTART_BASELINE.md`
- `docs/v2/14_CHAT_CLAIM_REGISTRY_CORRECTIONS.md`
- `docs/v2/15_SOURCE_RECOVERY_SLICE_01.md`
- `docs/v2/16_DOCUMENT_NAMESPACE_AUDIT.md`
- `docs/v2/17_V2_00_EVIDENCE_GATE_AUDIT.md`
- `docs/v2/18_REPOSITORY_EVIDENCE_SLICE_02.md`
- `docs/v2/19_RECOVERY_PR_EVIDENCE_SLICE_03.md`
- `docs/v2/20_RECOVERY_PR_CI_MAPPING_SLICE_04.md`

Duplicate numeric prefixes remain for two `13` files and two `14` files. Contents are distinct. No rename, delete or renumber has been performed.

## Verified architecture state

- Kimia is the final authority for customer Money, Gold, Coin and Currency balances.
- GoldPlatform is the final authority for physical Custody/Amanat.
- Ledger, Journal, Event, Idempotency and Projection artifacts are audit, workflow and reconciliation evidence only.
- Customer financial data must fail closed when verified Kimia account resolution is unavailable.
- Frontend must not calculate financial values, Weight750 or Rial/Toman conversions.
- Kimia Read and Kimia Write remain separate boundaries.
- Kimia Write remains disabled until exact request/response and reconciliation Ground Truth is approved.
- Static demos remain `SUPERSEDED — TECHNICAL PREVIEW ONLY — NOT PRODUCT EVIDENCE`.

## Shared Kimia conversation recovery

- Historical source preserved as `SRC-V2-0006`.
- 45 material claims extracted and classified.
- Evidence levels, conflicts, final status and V2 action recorded.
- Higher-priority runtime evidence corrected lower-priority chat classifications through a separate correction ledger.
- Unsupported Coin/Currency transaction sums, internal Wallet authority, missing-as-zero behavior, guessed Action defaults and chat-generated adapters as implementation remain rejected or superseded.

## Recovery PR evidence progress

### Metadata mapped

Exact Base SHA, Head SHA, Merge SHA, state and intended scope are recorded for:

- PR #149 — CP-06 Custody/Delivery recovery
- PR #150 — canonical Kimia Read recovery
- PR #153 — internal Balance Projection authority guard
- PR #175 — direct Settlement completion guard
- PR #186 — Customer Kimia source-state UX

### Exact-Head CI mapped

- PR #149 Head `925e2624...`
  - Backend RC2 Candidate #69 — `EXECUTED — PASS`
  - Backend RC1 Validation #217 — `EXECUTED — PASS`
- PR #150 Head `e5d61218...`
  - Backend RC2 Candidate #49 — `EXECUTED — PASS`
- PR #153 Head `7f2121d5...`
  - Backend RC1 Validation #224 — `EXECUTED — PASS`
- PR #175 Head `be966d97...`
  - Backend RC1 Validation #288 — `EXECUTED — PASS`
  - Operational Readiness #3 — `EXECUTED — PASS`
- PR #186 Head `10121aeb...`
  - Backend RC1 Validation #313 — `EXECUTED — PASS`
  - Customer Frontend #15 — `EXECUTED — PASS`
  - Frontend Release Validation #12 — `EXECUTED — PASS`
  - Operational Readiness #17 — `EXECUTED — PASS`

For these five PRs, metadata and exact-Head CI are now `VERIFIED — EXECUTED`.

Current canonical code equivalence and full capability closure remain unverified.

## V2 documentation CI checkpoints

Verified exact-SHA CI includes runs #346, #347, #350, #351, #352, #355, #358, #359, #360, #363, #366 and #367, all `EXECUTED — PASS` on their recorded Heads.

Every newer documentation Head requires its own exact-SHA CI verification.

## Current Gate status

- Baseline V2 documents: `PASS`
- Claim Registry and correction path: `PASS`
- Architecture boundaries: `PASS`
- No runtime/financial/Kimia Write change: `PASS`
- Current PR/Base/Head evidence slice: `PASS`
- Five key Recovery PR metadata mappings: `PASS`
- Five key Recovery PR exact-Head CI mappings: `PASS`
- Final V2-00 Head CI: `PENDING`
- Document namespace canonical: `FAIL — DUPLICATE NUMBERING`
- Complete branch Head SHA ledger: `INCOMPLETE`
- Complete all-PR SHA/CI ledger: `INCOMPLETE`
- Current canonical code equivalence for mapped PRs: `INCOMPLETE`
- Complete capability traceability: `INCOMPLETE`
- Broader Kimia, database, visual and production evidence: `INCOMPLETE`

## Safety result

- Feature code: unchanged
- Financial implementation: unchanged
- Kimia Write: unchanged and disabled
- Migration/database: unchanged
- API/OpenAPI: unchanged
- Frontend behavior: unchanged
- Branch/PR/file deletion: none
- History rewrite/rebase/force push: none

## Honest readiness decision

`V2-00 — GATE NOT PASSED — CONTINUE STRICT EVIDENCE RECOVERY`.

`V2-01` remains `NOT STARTED`. The next bounded work is to verify current canonical code equivalence for the mapped Recovery PRs and continue exact metadata/CI slices for additional PRs.