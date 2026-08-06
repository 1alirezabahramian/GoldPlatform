# GoldPlatform V2 — Source Recovery Slice 01

- Owner: Alireza Bahramian
- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Repository: `1alirezabahramian/GoldPlatform`
- Base branch: `recovery/rc2-product-rebuild`
- V2 branch: `v2/source-recovery-v2-00`
- Date: 2026-08-06
- Scope: restart-from-zero source recovery for Project State, Project Memory, Domain Workshop, historical execution logs and File Library evidence.

## 1. Exact-SHA CI checkpoint

- Restart baseline commit: `5c776c2d06899a0f174e3b4fc22f7567598f26a8`
- Workflow: `Backend RC1 Validation`
- Run: `#344`
- Result: `EXECUTED — PASS`

This verifies the documentation-only restart checkpoint. It does not prove completeness of business recovery or product readiness.

## 2. Project State drift — verified

Two case-different files exist on the same reference branch:

### `docs/PROJECT_STATE.md`

- Updated: 2026-08-06
- Canonical recovery branch: `recovery/rc2-product-rebuild`
- Declares `RELEASE CANDIDATE — FINAL AUDIT IN PROGRESS`
- Production Ready: `NOT CLAIMED`
- Kimia remains final authority for Money, Gold, Coin and Currency.
- Custody remains separate and GoldPlatform-authoritative.
- Lists PRs #187, #188 and #189 as merged delivery evidence.
- Lists unresolved Kimia binding/write, tenant, production, restore, authentication/OpenAPI and real-device blockers.

### `docs/project_state.md`

- Last update begins at 2026-07-18.
- Declares progress around 25% and `Phase 2 Started`.
- Describes Laravel 12, early OTP/authentication, local wallets, account-group synchronization and an early roadmap.
- Contains early operational notes and historical issues.

### Classification

- `docs/PROJECT_STATE.md`: `CURRENT RECOVERY STATE CANDIDATE — REQUIRES EXACT SHA/PR/CI TRACE`
- `docs/project_state.md`: `HISTORICAL STATE — SUPERSEDED AS CURRENT STATUS, PRESERVE AS EVIDENCE`

No file is deleted or renamed in V2-00. Case-collision risk remains for case-insensitive filesystems and must be resolved only through a documented, non-destructive normalization decision later.

## 3. File Library recovery — verified first pass

The following directly relevant sources were recovered from File Library:

1. `00_PROJECT_MEMORY.md`
   - Accepted / Living Ground Truth.
   - Requires Project Memory to be read before architecture, financial, Kimia, Wallet or Custody changes.
   - Locks `NO GUESSING — NO REINVENTING — NO SILENT CHANGES`.
   - Records Complex Backend — Simple Frontend and Kimia-first evidence priority.

2. `41_GOLDPLATFORM_DOMAIN_WORKSHOP_2026-07-28.md`
   - Accepted working contract.
   - Owner is Domain Expert and financial-rule authority.
   - Covers balances, products, pricing, orders, physical products, inventory, custody and delivery.

3. `instructions.txt`
   - Repeats the V2 safety contract: Kimia financial authority, Custody separation, exact Decimal, backend-only Rial/Toman conversion, dynamic Coin/Currency, read/write separation, no destructive Git operations and exact test/CI status vocabulary.
   - Classification: `OWNER-PROVIDED OPERATING CONTRACT`.

4. `eyvay.txt`
   - Historical AP-01..AP-20 consolidation evidence.
   - Explicitly reports many CI states as unverified and identifies AP-06 as the only new migration in that historical stack.
   - Classification: `HISTORICAL CONSOLIDATION EVIDENCE — VERIFY AGAINST GITHUB BEFORE CANONICAL USE`.

5. Historical PowerShell/test outputs (`Pasted text.txt` variants)
   - Include Docker runtime, Laravel tests, route lists, early commits and Kimia-related test output.
   - Classification: `HISTORICAL EXECUTION EVIDENCE`.
   - These logs are not automatically current-branch proof; each must be tied to repository SHA and environment before reuse.

6. Duplicate Project Memory copies such as `00_PROJECT_MEMORY(14).md`
   - Same broad source family with later sections and operational evidence.
   - Classification: `DUPLICATE/DERIVED COPY CANDIDATE` until content hashes and differences are compared.

## 4. Kimia evidence recovered in this slice

Project Memory and historical execution evidence support these currently traceable read-only facts:

- `GET /api/account` uses query `Type`.
- `GET /api/account/groups` uses query `accountType`.
- `GET /api/voucher/balance/{id}` is a read-only balance path.
- Real/historical account inspection evidence references AccountId `350` only as an approved evidence example, not as a universal identifier.
- Historical runtime evidence records `Action 32=خرید` and `Action 64=فروش` for the inspected transaction context.
- Operational/form codes remain distinct from API transport actions.

Strict limits:

- These facts do not authorize Kimia Write.
- Coin/Currency payload details remain unverified.
- Endpoint-specific Action meanings must not be generalized.
- Historical logs require exact SHA/environment correlation before becoming current implementation proof.

## 5. Source trust classification

| Source | Classification | Current use |
|---|---|---|
| Current GitHub branch/PR/SHA/CI | `CURRENT DEVELOPMENT TRUTH` | Canonical implementation/status evidence |
| Real sanitized Kimia API output | `HIGHEST FINANCIAL GROUND TRUTH` | Endpoint-specific behavior only |
| Official Swagger/OpenAPI | `FORMAL CONTRACT EVIDENCE` | Below real output |
| Accepted Project Memory | `ACCEPTED LIVING GROUND TRUTH` | Architecture/rule baseline |
| Accepted Domain Workshop | `OWNER-ACCEPTED DOMAIN EVIDENCE` | Business-rule extraction |
| Owner instructions | `OWNER-PROVIDED OPERATING CONTRACT` | Safety and process rules |
| Historical chat/log exports | `HISTORICAL/MIXED-TRUST EVIDENCE` | Leads and corroboration only |
| Duplicate memory copies | `DUPLICATE/DRIFT CANDIDATE` | Compare before use |

## 6. Verified gaps after Slice 01

1. Full content comparison between all Project Memory copies.
2. Full ADR inventory across `docs/ADR/` and `docs/adr/`.
3. Exact CHANGELOG inventory and current-state alignment.
4. Exact official Kimia Swagger file and hash.
5. Sanitized real Kimia response corpus with endpoint/date/SHA linkage.
6. Full ZIP archive recovery and contents inventory.
7. Database export/applied-migration evidence.
8. Full Branch → Head SHA and PR → Merge SHA → CI ledgers.
9. Full capability-to-code traceability.
10. Real-device and production evidence.

## 7. Honest status

- Restart baseline: `VERIFIED — EXECUTED`
- Restart baseline CI: `EXECUTED — PASS`
- Project State comparison: `VERIFIED — EXECUTED`
- File Library recovery: `EXECUTED — FIRST PASS`
- Kimia evidence recovery: `EXECUTED — FIRST PASS — READ-ONLY`
- Full source recovery: `INCOMPLETE`
- V2-00: `IN PROGRESS`
- V2-01: `NOT STARTED`
- Production Ready: `NOT CLAIMED`
