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
**Decision:** Operational/form codes and Swagger API Actions are distinct. For confirmed paper-gold behavior: customer buy maps to Kimia sell/API Action `64`; customer sell maps to Kimia buy/API Action `32`. Operational codes `4` and `3` must not be sent as API Actions.

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

## Pending decisions

| ID | Topic | Why pending |
|---|---|---|
| V2-P-0001 | Tenant/Company/Branch model | Requires complete evidence and owner architecture decision |
| V2-P-0002 | Approved Kimia write gateway | Requires real payload/result ground truth |
| V2-P-0003 | Pricing/commission/freeze/credit registry | Requires full rule recovery and conflict resolution |
| V2-P-0004 | Canonical ADR numbering and directory | Requires complete ADR inventory |
| V2-P-0005 | Canonical Project State document | Requires comparison of duplicate state files |
