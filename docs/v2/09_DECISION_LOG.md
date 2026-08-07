# GoldPlatform V2 — Decision Log

**Stage:** V2-00  
**Purpose:** Record decisions without rewriting historical ADRs.

## V2-D-0001 — Preserve V1 as evidence

**Status:** ACCEPTED  
**Decision:** V1 branches, PRs, commits, migrations, documents, tests and demos remain preserved. V2 is reconstruction from evidence, not a destructive rewrite.

## V2-D-0002 — Canonical V2 evidence base

**Status:** ACCEPTED FOR V2-00  
**Decision:** V2-00 is based on `recovery/rc2-product-rebuild` at start SHA `cd92a1144bdfbe043bae1871aab9d623ce8bad64`. `main` and historical branches remain comparison sources and must not be merged or cherry-picked blindly.

## V2-D-0003 — Financial source of truth

**Status:** ACCEPTED  
**Decision:** Kimia is final authority for Money, Gold, Coin and Currency. GoldPlatform does not maintain a competing customer balance.

## V2-D-0004 — Custody source of truth

**Status:** ACCEPTED  
**Decision:** GoldPlatform is final authority for physical Custody/Amanat. Custody is separate from financial balances.

## V2-D-0005 — Internal accounting components

**Status:** ACCEPTED  
**Decision:** Ledger, Journal, Event Store, Idempotency, Reservation and Projection support audit, intent/result, workflow and reconciliation only. They are not final customer balance authority.

## V2-D-0006 — Kimia code systems

**Status:** ACCEPTED FROM RECOVERED RUNTIME EVIDENCE  
**Decision:** Operational/form codes and Swagger API Actions are distinct. For the recorded AccountId `350` paper-gold behavior: customer buy maps to Kimia sell/API Action `64`; customer sell maps to Kimia buy/API Action `32`. Operational codes `4` and `3` must not be sent as API Actions.

**Boundary:** This is a limited observed mapping, not a universal endpoint dictionary and not authorization for Kimia Write.

## V2-D-0007 — Kimia Write

**Status:** BLOCKED BY GROUND TRUTH  
**Decision:** Kimia Write remains deny-by-default. Swagger and historical adapters are evidence but do not authorize writes. Approval requires real sanitized payload/result evidence, idempotency, post-write readback and reconciliation.

## V2-D-0008 — Financial precision and units

**Status:** ACCEPTED  
**Decision:** Money, weight, price and quantity use Decimal or decimal strings, never float. Kimia uses Rial; platform presentation uses Toman. Conversion is centralized and tested in Backend only.

## V2-D-0009 — Frontend boundary

**Status:** ACCEPTED  
**Decision:** Frontend performs no financial calculation, Weight750 calculation, balance mutation or direct Kimia access. It displays simple Persian customer concepts and preserves unavailable data as unavailable.

## V2-D-0010 — Demo classification

**Status:** ACCEPTED  
**Decision:** Previous HTML/static demos are `SUPERSEDED — TECHNICAL PREVIEW ONLY — NOT PRODUCT EVIDENCE`. Final visual verification must use executable Frontend and real Backend contracts.

## V2-D-0011 — Closed PR classification

**Status:** ACCEPTED  
**Decision:** Closed — Not Merged PRs are historical evidence, not canonical implementation. Merged status alone also does not prove completeness or Production readiness.

## V2-D-0012 — CI honesty

**Status:** ACCEPTED  
**Decision:** Tests written, tests executed, PR-Head CI and merge-SHA CI are recorded separately. Production Ready cannot be claimed without the required green CI on exact relevant SHA plus environment evidence.

## V2-D-0013 — Duplicate handling

**Status:** ACCEPTED  
**Decision:** Existing duplicates are preserved during V2-00. Canonical selection requires comparison and documentation before rename, removal or integration.

## V2-D-0014 — Historical conversations use Claim Registry

**Status:** ACCEPTED  
**Decision:** A historical conversation is integrated into V2 only through a Claim Registry containing Source, Evidence Level, Conflict/Unknown, final classification and V2 action. Conversation text and generated code are not canonical Ground Truth or implementation evidence by themselves.

**Evidence:**
- `docs/v2/13_CHAT_CLAIM_REGISTRY_SHARED_KIMIA_CONVERSATION.md`
- `docs/v2/14_CHAT_CLAIM_REGISTRY_CORRECTIONS.md`

## V2-D-0015 — Higher-priority evidence corrects classification without destroying history

**Status:** ACCEPTED  
**Decision:** When a higher-priority source is recovered, the original claim remains preserved. A linked correction record updates the final V2 classification and explains the prior conflict. Silent rewriting of historical evidence is prohibited.

## V2-D-0016 — Missing financial information is not zero

**Status:** ACCEPTED  
**Decision:** Missing, unresolved or unavailable Kimia financial data remains unavailable. It must not be presented as valid zero. Valid zero, negative balance and unavailable/error are distinct states.

## V2-D-0017 — Transaction aggregation is not Coin/Currency final balance authority

**Status:** ACCEPTED  
**Decision:** Coin or Currency transaction-history sums cannot become final customer balances. An authoritative Kimia read source must be recovered. Any local projection must be Kimia-derived, timestamped, rebuildable and reconcilable.

## V2-D-0018 — Generated Kimia adapters remain proposals until GitHub proves implementation

**Status:** ACCEPTED  
**Decision:** Chat-generated classes such as `TransactionAdapter`, `GoldTradeAdapter`, `CoinTradeAdapter`, `CashTradeAdapter`, `ActionMapper` and command/result DTOs remain `CHAT-ONLY PROPOSAL` unless exact files, branch, commit, PR, tests and exact-SHA CI prove otherwise. Existing canonical equivalents must be searched before creation.

## Pending decisions

| ID | Topic | Why pending | Current evidence link |
|---|---|---|---|
| V2-P-0001 | Tenant/Company/Branch model | Requires complete evidence and owner architecture decision | Gap/Drift and Capability Matrix |
| V2-P-0002 | Approved Kimia write gateway | Requires real payload/result ground truth | `KC-007`, `KC-008`, `KC-012`, `KC-025`, `KC-026`, `KC-029` |
| V2-P-0003 | Pricing/commission/freeze/credit registry | Requires full rule recovery and conflict resolution | `KC-022`, `KC-023`, `KC-024` plus Project Memory/Domain Workshop |
| V2-P-0004 | Canonical ADR numbering and directory | Requires complete ADR inventory | Repository evidence ledger |
| V2-P-0005 | Canonical Project State document | Requires comparison of duplicate state files | Source Index / Gap report |
| V2-P-0006 | Customer registration and Kimia account lifecycle | Requires evidence for create/link/match/approval/failure behavior | `KC-031` |
| V2-P-0007 | Default Kimia customer group policy | Requires source recovery and owner decision only after evidence exhaustion | `KC-031` |
| V2-P-0008 | Custody conversion/resale semantics | Requires owner-confirmed business rule and Kimia impact evidence | Claim Registry owner-decision section |
| V2-P-0009 | Approved sanitized Kimia evidence set | Requires selecting safe real accounts/transactions without exposing secrets or customer data | Kimia Ground Truth unresolved evidence |

## Decision timing rule

Pending decisions do not stop documentation and repository recovery. They stop only the implementation or activation that depends on them. The owner is not asked to restate existing rules until all available Project Memory, ADR, Domain Workshop, GitHub, Swagger and real-output evidence has been exhausted.