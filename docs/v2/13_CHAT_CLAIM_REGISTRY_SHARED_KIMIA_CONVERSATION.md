# GoldPlatform V2 — Claim Registry: Shared Kimia Conversation

- Owner: Alireza Bahramian
- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Registry ID: `CR-CHAT-KIMIA-0001`
- Source ID: `SRC-V2-0006`
- Source title: `Kimia API Integration Plan | Shared Conversation`
- Shared URL: `https://chatgpt.com/share/6a748def-2640-83eb-87dd-0f3cb629c37e`
- Preserved export: uploaded `gp.txt`
- Classification: `HISTORICAL CONVERSATION EVIDENCE`
- Review date: 2026-08-06
- Status: `REVIEWED — CLAIMS CLASSIFIED — NOT CANONICAL GROUND TRUTH`

## 1. Purpose

This document does not copy the conversation into V2 as truth. It extracts each material claim and records:

- what was claimed;
- who or what the source was;
- evidence level;
- conflict or uncertainty;
- final V2 classification;
- required integration or follow-up.

A statement is not accepted merely because it appeared in chat. Development state requires GitHub and exact-SHA CI evidence. Financial and Kimia behavior follows the project Ground Truth priority.

## 2. Evidence levels

| Level | Meaning |
|---|---|
| `E0 — CHAT ASSERTION` | Stated by the assistant or user in conversation, without independent evidence attached. |
| `E1 — OWNER-CONFIRMED DOMAIN RULE` | Explicitly confirmed by Alireza as business/domain knowledge. |
| `E2 — PROJECT DOCUMENT EVIDENCE` | Supported by Project Memory, accepted ADR, Domain Workshop or another controlled project document. |
| `E3 — OPENAPI / SCHEMA EVIDENCE` | Supported by official Kimia Swagger/OpenAPI schema; proves contract shape, not necessarily runtime semantics. |
| `E4 — GITHUB IMPLEMENTATION EVIDENCE` | Supported by canonical code, commit or merged PR. |
| `E5 — EXACT-SHA TEST / CI EVIDENCE` | Tests executed and passed on the exact relevant SHA. |
| `E6 — REAL KIMIA OUTPUT` | Supported by captured real API request/response or equivalent sanitized runtime evidence. Highest Kimia evidence. |

## 3. Final classifications

| Classification | Meaning |
|---|---|
| `ACCEPTED FOR V2` | May be used as a V2 rule or architecture boundary. |
| `ACCEPTED WITH REVALIDATION` | Useful evidence, but exact current contract or implementation must be rechecked. |
| `BLOCKED BY GROUND TRUTH` | Must not be implemented until real Kimia or owner decision resolves it. |
| `CHAT-ONLY PROPOSAL` | Design suggestion only; not implementation evidence. |
| `HISTORICAL ONLY` | Describes V1 or an earlier state and must not drive V2 directly. |
| `SUPERSEDED` | Replaced by a later accepted architecture or decision. |
| `UNSUPPORTED — REJECT` | Lacks sufficient evidence or conflicts with a higher-priority source. |
| `DUPLICATE / CONSOLIDATE` | Repeated material that should live in one canonical V2 registry. |

## 4. Claim registry

| ID | Claim | Source | Evidence | Conflict / uncertainty | Final status | V2 action |
|---|---|---|---|---|---|---|
| KC-001 | Kimia is the final authority for customer Money, Gold, Coin and Currency balances. | Owner rules, Project Memory, current GitHub recovery PRs | E1 + E2 + E4 | None found in current accepted architecture. | `ACCEPTED FOR V2` | Keep in Architecture Contract, Business Rule Registry and Kimia Ground Truth. |
| KC-002 | GoldPlatform is the final authority for physical Custody/Amanat. | Owner rules, Project Memory, current GitHub recovery PRs | E1 + E2 + E4 | Older chat wording sometimes mixed AccountType 10 with platform Custody storage. | `ACCEPTED FOR V2` | Preserve platform Custody authority; do not infer Kimia AccountType 10 is the platform custody model. |
| KC-003 | Ledger, Journal, Event, Idempotency and Balance Projection are audit/workflow/reconciliation artifacts, not customer balance authority. | Current project instructions and GitHub architecture boundaries | E1 + E2 + E4 | Older V1 Wallet and projection code may look like independent balance authority. | `ACCEPTED FOR V2` | Classify old balance stores as historical/projection unless proven otherwise. |
| KC-004 | Coin and Currency are dynamic and their identifiers must not be hard-coded. | Owner rule, Swagger endpoints, current architecture | E1 + E3 + E4 | Example IDs `10006`, `10007` appeared in chat. | `ACCEPTED FOR V2` | Keep dynamic discovery rule; mark sample IDs historical examples only. |
| KC-005 | Financial decimal values must not use float; Frontend must not calculate financial values, Weight750 or Rial/Toman conversion. | Owner rules and merged frontend safety PRs | E1 + E2 + E4 | Conversation occasionally presented arithmetic as if ready for implementation. | `ACCEPTED FOR V2` | Enforce Backend-only Decimal/String Decimal contracts and tests. |
| KC-006 | Kimia Read and Write must be separated; Controller must not call Kimia Client directly. | Owner architecture rule and current recovery boundaries | E1 + E2 + E4 | Proposed folder/class names in chat are not accepted implementation by themselves. | `ACCEPTED FOR V2` | Preserve as architecture contract; inventory canonical classes separately. |
| KC-007 | Kimia Write must remain disabled until exact request, response, action mapping and reconciliation ground truth are approved. | Current owner instructions and merged fail-closed guards | E1 + E2 + E4 | The chat later supplied executable-looking write adapters with default Action values. | `ACCEPTED FOR V2` | Keep writes blocked; classify adapter code as proposal only. |
| KC-008 | After a successful Kimia Write, balances must be read back from Kimia. | Owner rule and settlement safety boundary | E1 + E2 + E4 | No complete successful production write/readback trace is attached to this conversation. | `ACCEPTED FOR V2` | Keep as mandatory future write workflow and reconciliation gate. |
| KC-009 | `/api/account`, `/api/account/groups`, product, balance, transaction and voucher endpoints exist as listed. | Conversation interpretation of Swagger | E3 | Exact current Swagger file/version must be re-fetched and compared; chat may include omissions or naming drift. | `ACCEPTED WITH REVALIDATION` | Rebuild endpoint catalog from official current Swagger and real outputs. |
| KC-010 | `pageNumber` for transaction pagination starts at zero. | Swagger-derived statement | E3 | No real paginated response attached to this chat. | `ACCEPTED WITH REVALIDATION` | Verify against current OpenAPI and one sanitized real response. |
| KC-011 | Money and Weight balances may be negative. | Owner-confirmed business rule and schema compatibility | E1 + E2 + E3 | Swagger type alone does not prove business permission, but owner confirms negative balances. | `ACCEPTED FOR V2` | Preserve signed Decimal handling; never clamp to zero. |
| KC-012 | `RequestId` provides idempotency for Kimia write requests. | Swagger-derived statement | E3 | Presence, uniqueness scope, replay behavior and response semantics are not proven by real output. | `BLOCKED BY GROUND TRUTH` | Record schema support; do not assume full idempotency semantics until runtime tests exist. |
| KC-013 | Account types are `1,3,5,6,8,9,10,11,12` with the labels listed in chat. | Swagger-derived statement | E3 | Must be checked against current official Swagger and real account responses; account type is not the same as platform domain authority. | `ACCEPTED WITH REVALIDATION` | Add to Kimia dictionary with evidence level, not as platform business model. |
| KC-014 | Action codes are `1=receive, 2=pay, 3=buy, 4=sell, 7=transfer, 8=coin/currency conversion`. | Historical Project Memory/chat claim | E0/E2 historical | Conflicts with Swagger-derived `2,4,32,64...` values and endpoint-specific actions. | `BLOCKED BY GROUND TRUTH` | Do not implement or expose as final mapping. Preserve conflict. |
| KC-015 | Action codes are `2=receive, 4=pay, 32=buy, 64=sell`, with additional bit-like values in RecordDto. | Swagger-derived chat claim | E3 | Conflicts with historical project mapping; runtime meaning may differ by endpoint/request/record. | `BLOCKED BY GROUND TRUTH` | Require real request/response evidence for each operation and endpoint. |
| KC-016 | `ProductId = 4` means `پولی` for Paper Gold. | Swagger example and historical examples | E3 at best | Example schema is not proof across book/tenant/version; no real product/transaction response attached. | `BLOCKED BY GROUND TRUTH` | Verify through real product catalog and real Paper Gold transaction. Never hard-code beforehand. |
| KC-017 | `Conversion Code = 8` is used for coin/currency monetization. | Historical chat/Project Memory claim | E0/E2 historical | Not directly confirmed in the cited Swagger schema and conflicts with endpoint/action interpretation. | `BLOCKED BY GROUND TRUTH` | Preserve as unresolved historical claim only. |
| KC-018 | Gold units are `0=mithqal, 1=gram, 2=ounce, 3=kilogram`. | Swagger-derived claim | E3 | Must be checked against exact current schema and request behavior. | `ACCEPTED WITH REVALIDATION` | Add to Kimia dictionary with schema evidence. |
| KC-019 | Coin type codes are `15=bank coin`, `17=other coin`. | Swagger-derived claim | E3 | Current schema and runtime still need verification. | `ACCEPTED WITH REVALIDATION` | Retain as integration dictionary candidate, never frontend terminology. |
| KC-020 | Coin balance should be calculated by summing transaction Quantity per ProductId. | Assistant inference | E0 | No real Kimia balance contract or reconciliation evidence supports this; risks creating a competing balance. | `UNSUPPORTED — REJECT` | Do not implement. Coin balance must come from Kimia-authoritative read evidence. |
| KC-021 | Currency balance should be calculated from transaction history. | Assistant inference | E0 | Same authority and reconciliation problem as KC-020. | `UNSUPPORTED — REJECT` | Do not implement. Recover authoritative Kimia read contract. |
| KC-022 | `Weight750 = Weight × Fineness / 750`. | General industry reasoning in chat | E0/E1 candidate | Not proven as exact Kimia formula in the conversation; rounding and unit behavior are unknown. | `BLOCKED BY GROUND TRUTH` | Keep as owner/domain candidate; require accepted rule and backend tests before implementation. |
| KC-023 | Paper Gold amount is always `Weight × GoldPrice`. | Chat example and general reasoning | E0/E1 candidate | GoldUnit, commissions, rounding, Rial/Toman and Kimia semantics may change the exact formula. | `BLOCKED BY GROUND TRUTH` | Recover accepted pricing rule and actual Kimia transaction examples. |
| KC-024 | Coin/currency amount is always `Quantity × UnitPrice`. | Chat inference | E0 | Fees, divide-price semantics, rounding and endpoint meaning are not proven. | `BLOCKED BY GROUND TRUTH` | Do not encode until rule and output evidence exist. |
| KC-025 | Paper Gold buy/sell should use `/api/voucher/exchangegold`. | Assistant design inference from endpoint name/schema | E0 + E3 | Endpoint existence does not prove GoldPlatform business operation mapping. | `BLOCKED BY GROUND TRUTH` | Require real Kimia examples and owner-approved semantic mapping. |
| KC-026 | Coin buy/sell should use `/api/voucher/exchangecurrency`. | Assistant design inference | E0 + E3 | Endpoint semantics, SourceId/TargetId and action direction remain unverified. | `BLOCKED BY GROUND TRUTH` | No write implementation until real request/response evidence. |
| KC-027 | A transaction adapter should isolate raw Kimia codes and payloads from Domain and Controllers. | Assistant architecture proposal aligned with accepted boundaries | E0 + E2 | Exact class names, folders and DTO shapes are not canonical implementation evidence. | `ACCEPTED WITH REVALIDATION` | Reuse principle, inspect existing canonical implementation before creating anything. |
| KC-028 | Proposed classes `TransactionAdapter`, `GoldTradeAdapter`, `CoinTradeAdapter`, `CashTradeAdapter`, `ActionMapper` and command/result DTOs exist. | Assistant-generated sample code | E0 | No GitHub file/commit/PR/CI evidence in this conversation. | `CHAT-ONLY PROPOSAL` | Search canonical repo before any creation; report duplicate candidates. |
| KC-029 | Unknown Action codes may safely be placed in config with defaults `32/64/2/4`. | Assistant proposal | E0 | Defaults would activate guessed financial behavior and violate NO GUESSING. | `UNSUPPORTED — REJECT` | Do not use configurable guessed defaults for Kimia writes. Fail closed instead. |
| KC-030 | Registration currently creates User, Wallet, RIAL and GOLD18 accounts. | Assistant description of older code | E0/E4 historical, exact SHA not attached | May describe V1 code but conflicts with final balance authority if treated as customer balance. | `HISTORICAL ONLY` | Inventory exact historical files/commit; do not carry independent balances into V2. |
| KC-031 | Registration should immediately POST an account to Kimia and assign a default group. | Assistant target-flow proposal | E0 | Account matching, duplicate handling, approval and group assignment require owner decision and real Kimia contract. | `BLOCKED BY GROUND TRUTH` | Record as open architecture/business decision, not an implementation plan. |
| KC-032 | V1 TradeService/Order flow used hard-coded internal account IDs and local Ledger completion. | Assistant code review claim | E0/E4 historical, exact SHA not attached | Must be verified against preserved V1 commit; current recovery branch includes guards against direct completion. | `HISTORICAL ONLY` | Locate exact file/SHA and classify drift; do not reuse blindly. |
| KC-033 | Wallet internal balances should be updated after Kimia trade. | Assistant target-flow proposal | E0 | Conflicts with Kimia as final balance authority unless explicitly a rebuildable snapshot/projection. | `SUPERSEDED` | Replace with Kimia-derived snapshot/projection terminology and reconciliation requirements. |
| KC-034 | BalanceMapper may default missing money/gold to zero. | Assistant sample code | E0 | Conflicts with merged frontend/customer rule that unavailable financial information must not be shown as zero. | `UNSUPPORTED — REJECT` | Missing/unresolved Kimia data must fail closed as unavailable. |
| KC-035 | Raw Kimia request payload may be logged for debugging. | Assistant sample implementation | E0 | May expose sensitive personal or financial data; conflicts with secret/data minimization rules. | `UNSUPPORTED — REJECT` | Log request ID, endpoint, status and sanitized diagnostics only. |
| KC-036 | Kimia HTTP retry can be common for GET/POST/PUT/DELETE. | Description of older `KimiaService` | E0/E4 historical | Current architecture explicitly requires Read and Write retry policies to differ. | `SUPERSEDED` | Audit canonical client and enforce no unsafe automatic mutation retry. |
| KC-037 | Multiple parallel Kimia services/repositories and config keys existed. | Assistant code audit claim | E0/E4 historical | Exact branch and files were not attached; current canonical code may have replaced them. | `HISTORICAL ONLY` | Verify in Git history and classify removed/reused implementations. |
| KC-038 | `Account::count() = 0` means Kimia API is broken. | Potential inference discussed in chat | E0 | Conversation correctly warned this alone does not prove API failure. | `UNSUPPORTED — REJECT` | Diagnose connection, filters, mapping, persistence and errors separately. |
| KC-039 | Example JSON values shown in chat are real Kimia responses. | Assistant wording around examples | E0 | Most examples appear schema-derived or illustrative; no raw capture metadata is attached. | `UNSUPPORTED — REJECT` | Label examples as schema/illustrative unless an immutable raw evidence file is linked. |
| KC-040 | The Swagger file alone was sufficient to finalize the Kimia adapter. | Implied by some design sections | E0 | Violates Ground Truth order and real-output requirement. | `UNSUPPORTED — REJECT` | Swagger may shape read DTOs, but writes and financial semantics stay blocked. |
| KC-041 | Static HTML prototypes represent implemented product capability. | Historical chat/project behavior | E0/E4 demo only | Current accepted rule classifies them as disconnected fictional previews. | `SUPERSEDED` | Keep `SUPERSEDED — TECHNICAL PREVIEW ONLY — NOT PRODUCT EVIDENCE`. |
| KC-042 | Laravel 12 and the described code structure are current project state. | Historical conversation | E0/E4 historical | Later repository state reports newer framework/code and must be read from the current branch. | `HISTORICAL ONLY` | Resolve current versions from canonical lockfiles and exact SHA. |
| KC-043 | Kimia Read-only foundation is actually implemented. | GitHub merge commit and recovery evidence | E4; exact capability CI ledger still being completed | Must not be expanded into Write readiness or customer balance availability. | `ACCEPTED FOR V2` | Link exact files, tests, merge SHA and CI in Capability Matrix. |
| KC-044 | Customer real financial balances are currently available end-to-end. | Some target-flow wording in chat | E0 | Current merged frontend explicitly fails closed until authenticated customer-to-Kimia account resolution exists. | `UNSUPPORTED — REJECT` | Keep capability blocked; do not substitute internal or zero balances. |
| KC-045 | The proposed transaction adapters were implemented and tested. | Assistant-provided code snippets | E0 | No exact file, branch, commit, PR or CI evidence. | `CHAT-ONLY PROPOSAL` | Search before implementation; status remains `NOT IMPLEMENTED` unless GitHub proves otherwise. |

## 5. Conflicts requiring permanent tracking

### CF-KIMIA-001 — Action code conflict

- Historical mapping: `1/2/3/4/7/8`
- Swagger-derived mapping: `2/4/32/64/...`
- Risk: wrong financial operation, reversed direction or invalid Kimia write.
- Status: `BLOCKED BY GROUND TRUTH`
- Resolution evidence required: sanitized real request/response for each intended operation, exact endpoint, Action, product/source/target IDs, result semantics and balance readback.

### CF-KIMIA-002 — ProductId 4 meaning

- Claim: ProductId `4` is `پولی`.
- Risk: hard-coded identifier may vary by Kimia book/environment/version.
- Status: `BLOCKED BY GROUND TRUTH`
- Resolution evidence required: current product catalog plus real Paper Gold transaction evidence.

### CF-KIMIA-003 — Coin/Currency balance authority

- Chat inference: calculate from transaction history.
- Accepted architecture: Kimia is final authority; GoldPlatform must not create a competing balance.
- Status: `UNSUPPORTED — REJECT`
- Resolution: locate authoritative Kimia read output; projections may only be Kimia-derived, timestamped, rebuildable and reconcilable.

### CF-ARCH-001 — Wallet authority drift

- Historical V1: internal RIAL/GOLD18 Wallet accounts.
- Accepted V2: no independent final balance for Money/Gold/Coin/Currency.
- Status: `SUPERSEDED`
- Resolution: classify legacy wallet tables/services as historical, audit/workflow or projection artifacts only where justified.

### CF-UX-001 — Missing value displayed as zero

- Chat sample mapper returned zero when Kimia balance was missing.
- Merged current UX contracts require unavailable data to remain unavailable.
- Status: `UNSUPPORTED — REJECT`
- Resolution: fail closed with unavailable/error state.

## 6. Claims requiring owner decision

These decisions cannot be derived safely from the conversation alone:

1. Customer registration and Kimia account lifecycle:
   - immediate Kimia account creation;
   - operator-approved creation;
   - linking an existing account;
   - duplicate matching and failure handling.
2. Default Kimia customer group and who is authorized to assign/change it.
3. Business meaning of buy/sell direction from the customer and shop perspectives; numeric Kimia codes still require real API evidence.
4. Rules for converting physical Custody to Money or Gold, including approval, pricing time, partial conversion and delivery implications.
5. Which sanitized real Kimia accounts/transactions may be used to resolve Action, Product and response semantics.

No decision is requested merely to continue documentation recovery. These items remain recorded until their implementation stage requires them.

## 7. GitHub implementation findings connected to this source

The conversation must not be used to claim implementation. Current GitHub evidence separately supports:

- merged Kimia read-only foundation;
- financial mutation and settlement fail-closed guards;
- customer financial pages that remain unavailable until verified customer-to-Kimia resolution;
- frontend prohibition on financial calculations and zero substitution;
- separated Custody presentation;
- static demos classified as non-product evidence.

Exact Capability → File → Test → SHA → CI closure remains part of the V2-00 traceability audit.

## 8. Integration result

### Accepted into V2

- Kimia final balance authority.
- GoldPlatform physical Custody authority.
- Dynamic Coin/Currency identifiers.
- Decimal and Backend-only financial logic.
- Read/Write separation.
- Adapter boundary principle.
- Fail-closed behavior for unknown Kimia mappings.
- Readback and reconciliation requirement after future writes.

### Preserved as unresolved evidence

- Action code mappings.
- ProductId `4` meaning.
- RequestId runtime idempotency semantics.
- exact exchange endpoint mapping for Gold/Coin/Currency.
- Weight750, pricing, unit and rounding formulas.
- registration-to-Kimia account workflow.

### Rejected or superseded

- deriving Coin/Currency balances from transaction history as final balances;
- guessed configurable Action defaults;
- internal Wallet as customer balance authority;
- mapping unavailable balances to zero;
- treating schema examples as real API outputs;
- treating generated adapter snippets as implemented capability;
- common automatic retry behavior for reads and writes;
- treating static demos as product evidence.

## 9. Traceability status

- Source preserved: `YES`
- Claims extracted: `YES`
- Evidence levels assigned: `YES`
- Conflicts recorded: `YES`
- Final classifications assigned: `YES`
- GitHub implementation independently verified for every claim: `NO — IN PROGRESS`
- Real Kimia evidence linked for every financial claim: `NO — BLOCKED / NOT YET RECOVERED`
- V2-00 closure impact: `PROGRESS MADE — CLOSURE NOT CLAIMED`
