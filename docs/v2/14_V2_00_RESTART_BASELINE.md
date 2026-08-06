# GoldPlatform V2 — Stage V2-00 Restart Baseline

- Owner: Alireza Bahramian
- Repository: `1alirezabahramian/GoldPlatform`
- Evidence base: `recovery/rc2-product-rebuild`
- V2 working branch: `v2/source-recovery-v2-00`
- Restart date: 2026-08-06
- Status: `IN PROGRESS — RESTARTED FROM ZERO WITH STRICT EVIDENCE`

## 1. Restart decision

V2-00 is restarted from zero as an evidence audit. Existing V2 documents are preserved as prior work and audit inputs, but none is treated as complete merely because it exists or passed CI.

A stage is accepted as `VERIFIED — EXECUTED` only when its applicable scope has immutable evidence: source, branch, exact SHA, PR, changed files, test status, exact-SHA CI, documentation, remaining gaps and contradiction review.

## 2. Verified GitHub starting state

- PR: `#195`
- State: `OPEN — DRAFT — NOT MERGED`
- Base branch: `recovery/rc2-product-rebuild`
- Base SHA: `cd92a1144bdfbe043bae1871aab9d623ce8bad64`
- Head before this restart commit: `474534bce9356972426eb7ebd255a339ad33c414`
- PR changes before this restart: documentation only under `docs/v2/`
- No product feature, migration, API, OpenAPI, permission, frontend behavior, financial rule or Kimia Write change is introduced by this restart.

## 3. Recovered source set — first strict pass

### SRC-RST-0001 — Accepted Project Memory

- Source: uploaded `00_PROJECT_MEMORY.md`
- Classification: `ACCEPTED / LIVING GROUND TRUTH`
- Verified content includes:
  - Complex Backend — Simple Frontend;
  - Kimia-first source hierarchy;
  - Money, Gold, Coin and Currency as financial balance types;
  - Custody as a separate physical concept;
  - negative balances;
  - dynamic Coin and Currency;
  - Kimia Read-only safety boundary;
  - platform-user to Kimia-account binding evidence.
- Caution: the document is living and contains historical implementation states; each development-status claim must be rechecked against current GitHub.

### SRC-RST-0002 — Accepted Domain Workshop

- Source: uploaded `41_GOLDPLATFORM_DOMAIN_WORKSHOP_2026-07-28.md`
- Classification: `OWNER-ACCEPTED DOMAIN EVIDENCE`
- Verified content includes:
  - owner/domain-expert role;
  - Kimia and GoldPlatform authority boundaries;
  - real balance response for AccountId 350;
  - meaning of `Money` depending on `CurrencyId`;
  - dynamic Currency and Coin catalogs;
  - immutable Kimia ID versus mutable shortcut code;
  - customer intent versus Kimia/store perspective;
  - contextual meaning of operational/form codes.
- Caution: operational/form codes are not automatically API `Action` values; endpoint-specific API evidence remains mandatory.

### SRC-RST-0003 — Kimia UI Evidence

- Source: uploaded `GoldPlatform-Kimia-UI-Evidence-2026-08-02.md`
- Classification: `OBSERVED KIMIA UI EVIDENCE`
- Verified content includes:
  - Account/Product ID and mutable Code are separate;
  - product types have different field sets;
  - customer-group and product-group concepts are distinct;
  - Product, Coin and Currency contracts must remain separated.
- Caution: a field visible in Kimia desktop UI does not prove that the public API exposes or accepts it. No DTO, migration, enum, formula or API mapping may be created from UI screenshots alone.

### SRC-RST-0004 — Historical Kimia conversation/export evidence

- Sources found: `gp.txt` and pasted command/log exports.
- Classification: `HISTORICAL / MIXED-TRUST EVIDENCE`
- Value: schemas, proposals, test output and historical reasoning.
- Caution: proposed adapters, default IDs, endpoint payloads and mappings are not Ground Truth unless independently supported by real API output or official Swagger. Some text explicitly labels values as provisional.

## 4. Immediate contradictions and cautions

1. Owner-facing operational/form codes and Swagger/API actions are separate code systems and must never be collapsed into a single global enum.
2. Historical material that proposes wallet updates after Kimia operations conflicts with the accepted rule that Kimia is the final balance authority; such material is historical design evidence only unless reframed as cache/reconciliation.
3. Kimia UI screenshots prove visible fields and ID/Code separation, not API write contracts.
4. Existing Rule Registry and Capability Matrix are partial inventories, not complete recovery.
5. Existing PR descriptions and documents may contain stale Head/CI wording; live GitHub metadata outranks embedded text.

## 5. Execution status vocabulary

- GitHub and source reads performed in this restart: `EXECUTED`
- Product tests: `NOT APPLICABLE` for this documentation-only slice
- Exact-SHA CI for the pre-restart Head: to be linked from GitHub Actions evidence
- Exact-SHA CI for this new restart commit: `PENDING`

## 6. Next controlled slices

1. GitHub repository state: exact branch/base/head/PR/CI evidence.
2. Source inventory: File Library, uploaded files, repository docs, ZIP and export discovery.
3. Project Memory and Domain Workshop extraction into source-addressable rule records.
4. Current canonical code tree inventory before any capability judgment.
5. PR/commit/CI traceability in bounded batches.

## 7. Honest decision

- V2-00 restarted from zero: `VERIFIED — EXECUTED`
- Source recovery: `IN PROGRESS — FIRST STRICT PASS`
- Full business-rule recovery: `INCOMPLETE`
- Full capability traceability: `INCOMPLETE`
- V2-01: `NOT STARTED`
- Production Ready: `NOT CLAIMED`
