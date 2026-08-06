# GoldPlatform V2 — Capability Traceability Matrix

- Stage: `V2-00`
- Status: `INITIAL INVENTORY — NOT COMPLETE`
- Rule: no capability is declared complete or absent until all traceability columns are verified.

## Status vocabulary

`REUSE AS-IS` · `REUSE AFTER FIX` · `REFACTOR` · `REBUILD` · `IMPLEMENTED — NOT TESTED` · `TESTED — NOT MERGED` · `MERGED — CLOSURE PENDING` · `BLOCKED BY GROUND TRUTH` · `DUPLICATE CANDIDATE` · `SUPERSEDED` · `HISTORICAL ONLY` · `NOT IMPLEMENTED`

## Initial capability matrix

| Capability | Primary evidence found | Current status | Tests / CI truth | Initial gap / risk | Recommended V2 action |
|---|---|---|---|---|---|
| Authentication | Laravel/Sanctum routes, OTP-related models/services, Customer/Backoffice guarded routes | `REUSE AFTER FIX` | Tests exist; full exact-SHA mapping pending | Registration/OTP/session/KYC paths require end-to-end inventory; historical mock/legacy paths may exist | Trace routes → services → tests → frontend before changing |
| OTP | OTP model/service and historical work | `REUSE AFTER FIX` | Execution evidence incomplete | Provider, expiry, retry, abuse limits and production delivery not yet traced | Audit only |
| Profile | Customer Profile read contract and frontend | `MERGED — CLOSURE PENDING` | PR/CI evidence exists historically; exact current SHA verification pending | Write/update/KYC/session management not proven | Reuse read path; classify writes separately |
| KYC | References in project scope, no verified complete flow yet | `NOT IMPLEMENTED` | Not yet proven | Do not infer fields/workflow | Continue source search before final classification |
| Customer Groups | Models/policies, Kimia group sync, historical Admin reads | `REUSE AFTER FIX` | Mixed canonical and closed-unmerged evidence | Kimia group vs platform group authority and policy linkage need reconciliation | Build source-to-code map |
| Money Balance | Customer reads fail closed unless Kimia account resolved | `BLOCKED BY GROUND TRUTH` | Boundary tests merged; live customer resolution not complete | Cannot expose internal balance projection | Implement only after account-resolution evidence |
| Gold Balance | Same Kimia authority boundary | `BLOCKED BY GROUND TRUTH` | Boundary tests merged | Unit/scale/read mapping require real-output verification | Audit mapper and raw-output evidence |
| Coin Balance | Dynamic coin catalog and Kimia reads exist | `BLOCKED BY GROUND TRUTH` | Read tests exist | Exact per-customer balance shape/source needs real-output proof; Claim `KC-020` rejects transaction-sum derivation | Do not derive from transaction history; recover Kimia-authoritative read output |
| Currency Balance | Dynamic currency catalog and Kimia reads exist | `BLOCKED BY GROUND TRUTH` | Read tests exist | Exact per-customer balance shape/source needs real-output proof; Claim `KC-021` rejects transaction-sum derivation | Same as Coin |
| Dashboard | Customer/Admin/Operator read UIs exist | `MERGED — CLOSURE PENDING` | Frontend CI history exists; current exact-SHA map pending | Customer financial cards intentionally unavailable without Kimia account mapping | Reuse non-financial and fail-closed patterns |
| Market | Demo and frontend UX references | `DUPLICATE CANDIDATE` | Demo-only work is not product proof | Real pricing source and contract not fully traced | Inventory backend pricing/quote evidence first |
| Pricing | Models/docs/historical Admin reads | `BLOCKED BY GROUND TRUTH` | Tests and docs exist in mixed histories | Formula, spread, rounding, source and units must not be inferred | Extract all owner-confirmed pricing rules |
| Quote | Project scope and order architecture | `NOT IMPLEMENTED` | Not yet proven | Quote lifecycle, expiry and price freeze require source reconstruction | Search all branches/PRs/docs |
| Buy | Order/trade scaffolding and guards | `BLOCKED BY GROUND TRUTH` | Tests exist for isolation/state machine, not approved Kimia execution | Runtime evidence distinguishes customer intent from Kimia side, but approved Write payload/idempotency/readback remain incomplete | Keep disabled; preserve `3/4` operational vs `32/64` API distinction |
| Sell | Same as Buy | `BLOCKED BY GROUND TRUTH` | Same | Same | Keep disabled; preserve endpoint-specific mapping |
| Conversion | Kimia Swagger/audit evidence | `BLOCKED BY GROUND TRUTH` | No approved live write proof | Endpoint-specific semantics, source/target IDs and RequestId behavior unresolved | Ground-truth recovery first |
| Order Lifecycle | `OrderStateMachine`, enums, Customer status APIs | `REUSE AFTER FIX` | Multiple tests/PRs merged | Must reconcile historical Stage 03 and current canonical transitions | Full state/permission/audit trace |
| Settlement | Settlement service, guards, outbox and docs | `REUSE AFTER FIX` | Boundary tests merged | External success cannot be inferred from Ledger; Kimia execution blocked | Preserve fail-closed design |
| Deposit | Scope references and wallet/payment artifacts | `NOT IMPLEMENTED` | Not yet proven | Bank gateway/receipt flows may exist only in demo/history | Search canonical and historical evidence |
| Payment Gateway | Demo UX only found in first pass | `HISTORICAL ONLY` | Not product-tested | No verified provider contract | Do not implement in V2-00 |
| Bank Receipt Upload | Demo UX and possible historical requirements | `HISTORICAL ONLY` | No canonical proof yet | Storage, review, audit and permissions unknown | Continue source recovery |
| Admin Receipt Approval | Demo/history evidence | `HISTORICAL ONLY` | No canonical proof yet | Workflow and financial authority unknown | Continue source recovery |
| Withdrawal | Demo/history evidence | `HISTORICAL ONLY` | No canonical proof yet | Money/Gold/Coin withdrawal rules unknown | Continue source recovery |
| Internal Transfer | Demo/history evidence | `HISTORICAL ONLY` | No canonical proof yet | Recipient confirmation and Kimia semantics unknown | Continue source recovery |
| Bank Accounts | Kimia wallet API mentioned in Swagger audit | `BLOCKED BY GROUND TRUTH` | No full canonical trace yet | Distinguish Kimia bank-wallet endpoints from platform customer bank accounts | Audit schemas and code |
| Products | Product models and Kimia dynamic reads | `REUSE AFTER FIX` | Tests exist | Authority split between local catalog and Kimia product identifiers | Build catalog authority map |
| Product Categories | Historical Admin read work | `HISTORICAL ONLY` | Closed-unmerged evidence | Canonical presence not yet confirmed | Compare later canonical replacements |
| Parsian | Domain Workshop/Project Memory | `BLOCKED BY GROUND TRUTH` | No complete trace yet | Product identity, barcode, pricing and custody conversion rules need extraction | Rule-by-rule recovery |
| Bullion | Domain Workshop/Project Memory | `BLOCKED BY GROUND TRUTH` | No complete trace yet | Same | Same |
| Coins | Kimia dynamic catalog and local cache models | `REUSE AFTER FIX` | Sync tests exist | Cache timestamp/rebuild/reconciliation proof pending; sample IDs from chat are historical only | Audit sync path and never hard-code identifiers |
| Currency | Kimia dynamic catalog and local cache models | `REUSE AFTER FIX` | Sync tests exist | Same | Audit sync path |
| Weight750 | Swagger field and historical domain examples | `BLOCKED BY GROUND TRUTH` | No approved formula proof | Claim `KC-022` preserves the common formula only as an unapproved candidate; unit/rounding behavior unresolved | No implementation change |
| Custody Creation | Custody model/service/customer resources | `REUSE AFTER FIX` | Tests merged | Full business rules and physical inventory linkage incomplete | Trace lifecycle and permissions |
| Custody Conversion | Domain Workshop references | `BLOCKED BY GROUND TRUTH` | Not proven | Conversion destination and financial effects must be owner/real-output confirmed | Keep blocked |
| Custody Resell | Domain references | `BLOCKED BY GROUND TRUTH` | Not proven | Pricing/settlement/Kimia impact unknown | Keep blocked |
| Physical Delivery | Delivery model/service/customer/operator paths | `REUSE AFTER FIX` | Tests and merged hardening PRs exist | Branch/inventory/receiver/approval policy trace incomplete | Audit end-to-end |
| Branch | Branch code appears in custody/delivery; no proven Branch entity | `BLOCKED BY GROUND TRUTH` | Historical read-only PR closed-unmerged | Tenant/company/branch architecture unresolved | Decision only after source exhaustion |
| Inventory | Barcode/RFID Swagger and project scope | `BLOCKED BY GROUND TRUTH` | No complete canonical trace | Local vs Kimia inventory authority unclear | Audit separately from Custody |
| Customer Support / Tickets | Demo/history requirements | `HISTORICAL ONLY` | No canonical product proof | Data model/API/permissions absent or unverified | Continue source search |
| Notifications | Outbox and provider interfaces exist | `REUSE AFTER FIX` | Outbox tests/guards merged | Approved handlers, channels and replay authority not complete | Preserve disabled automatic dispatch |
| Admin | Real frontend/read endpoints and hardening | `MERGED — CLOSURE PENDING` | CI history exists | Some historical AP capabilities remain closed-unmerged only | Map canonical replacement per capability |
| Operator | Real queue/action frontend/backend and permission gates | `MERGED — CLOSURE PENDING` | Tests/CI history exists | Branch scoping and full permission matrix need audit | Continue exact mapping |
| Roles / Permissions | Spatie foundation and explicit operator gates | `REUSE AFTER FIX` | Tests merged | Complete permission inventory and tenant scope unresolved | Build matrix from routes and middleware |
| Audit | AuditLog, APIs and redaction | `REUSE AFTER FIX` | Tests merged | Retention, actor scope and sensitive-data policy need full trace | Audit schema and lifecycle |
| Outbox | Model/service/dispatcher/guards | `REUSE AFTER FIX` | Tests merged | No approved production handlers; automatic scheduler disabled by default | Preserve fail-closed |
| Reconciliation | Architecture intent documented | `NOT IMPLEMENTED` | Not yet proven end-to-end | No complete Kimia snapshot/write-result reconciliation workflow | Design only after Ground Truth |
| Kimia Read | Canonical read client/repositories/sync commands | `REUSE AFTER FIX` | PR #150 and tests; exact capability CI ledger still incomplete | Real-output fixtures and account resolution incomplete | Deep audit first |
| Kimia Write | Preparation/deny-by-default artifacts only | `BLOCKED BY GROUND TRUTH` | Tests prove disabled boundary; Claim Registry documentation CI #346–#352 passed | Payload, endpoint-specific codes, RequestId semantics, error behavior and readback unresolved | Do not enable |
| White-label | Runtime brand configuration/frontends | `REUSE AFTER FIX` | Frontend tests exist | Tenant-specific data/config architecture not established | Separate branding from tenancy |
| Tenant Safety | Tenant-scoped financial kernel artifacts/history | `BLOCKED BY GROUND TRUTH` | Mixed merged/historical evidence | Canonical tenant/company model unclear | Architecture decision after inventory |
| Reports | Historical catalog/read ideas | `HISTORICAL ONLY` | Closed-unmerged AP evidence | Financial reports require Kimia authority and formula proof | Recover only verified operational reports |
| Monitoring | Operational health, workflows and security hardening | `REUSE AFTER FIX` | Tests/workflows exist | External production monitoring/alerts not proven | Keep honest environment status |
| Backup/Restore | Workflow and readiness docs | `MERGED — CLOSURE PENDING` | Workflow existence; exact current execution pending | Production restore drill evidence must be linked | Collect artifacts/runs |
| PWA | Customer PWA foundation merged | `MERGED — CLOSURE PENDING` | PR #189 history | Real-device install/offline/visual audit pending | Verify without caching financial API |
| Android | PWA support only | `NOT IMPLEMENTED` | N/A | No native package | Do not claim native app |
| iOS | PWA support only | `NOT IMPLEMENTED` | N/A | No native App Store package | Do not claim native app |
| Web | Customer/Admin/Operator Nuxt apps exist | `MERGED — CLOSURE PENDING` | CI history exists | Production deployment and visual verification pending | Audit exact builds/routes |
| Windows | PWA/browser support only | `NOT IMPLEMENTED` | N/A | No native Windows package | Do not claim native app |
| Demo and UX | Static demo merged through PR #194 | `SUPERSEDED` | Demo safety CI existed on PR heads | Fictional and disconnected | Use only as historical UX evidence |

## Shared Kimia conversation traceability

Source `SRC-V2-0006` is integrated through:

- `docs/v2/13_CHAT_CLAIM_REGISTRY_SHARED_KIMIA_CONVERSATION.md`
- `docs/v2/14_CHAT_CLAIM_REGISTRY_CORRECTIONS.md`

The registry changes capability classification only where a higher-priority source supports the correction. It does not create implementation evidence.

| Claim group | Capability impact | Final traceability result |
|---|---|---|
| `KC-001..KC-008` | Financial authority, Custody authority, Decimal, Read/Write separation | Accepted architecture boundaries; already represented in V2 contracts |
| `KC-014..KC-017` plus corrections | Buy/Sell and Kimia operational terminology | `3/4` operational/form and `32/64` API Action distinction recorded for the observed mapping; Write remains blocked |
| `KC-020..KC-021` | Coin/Currency balance | Transaction-history derivation rejected; authoritative Kimia read still required |
| `KC-022..KC-026` | Weight750, pricing and exchange endpoints | Still `BLOCKED BY GROUND TRUTH` |
| `KC-028..KC-029` | Proposed transaction adapters/default Action config | Chat-only or rejected; no implementation capability created |
| `KC-030..KC-036` | Registration, Wallet authority, unavailable-as-zero, logging and retry | Historical, superseded or rejected according to current V2 architecture |
| `KC-043..KC-045` | Kimia Read and adapter implementation claims | Read foundation accepted as GitHub evidence; proposed adapters remain unimplemented unless repository proof is found |

## Exact-SHA evidence for this recovery slice

- `e67b109df29188a1a0762681b8feb7394ab4d5bd` — Backend RC1 Validation #346 — `EXECUTED — PASS`
- `d86df86ab5ea2bd8639ced0d3087b0acf3575d14` — Backend RC1 Validation #347 — `EXECUTED — PASS`
- `795483794f024e03c7f52cd11123fa29150e4adc` — Backend RC1 Validation #350 — `EXECUTED — PASS`
- `9159392c9461bd3de3a9aa8aea15e8535759d060` — Backend RC1 Validation #351 — `EXECUTED — PASS`
- `23ac8e32e97e3187875fda6309636b0dbf187027` — Backend RC1 Validation #352 — `EXECUTED — PASS`

This update creates a newer Head; exact-SHA CI for that Head must be verified separately.

## Immediate traceability gaps

1. Exact current branch tree and all package paths.
2. Full PR ledger before #90 and every closed-not-merged stack classification.
3. Exact CI status for the newest V2 documentation Head and canonical merge SHA.
4. Complete real Kimia sanitized request/response evidence set.
5. Authenticated customer-to-Kimia account resolution.
6. Pricing/quote/freeze/commission/credit rules and supersession history.
7. Tenant/company/branch authority model.
8. Database export and migration-applied-state evidence.
9. Visual verification for real frontends.
10. Production deployment, monitoring and backup/restore artifacts.
