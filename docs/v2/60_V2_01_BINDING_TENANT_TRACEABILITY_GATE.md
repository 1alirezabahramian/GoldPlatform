# GoldPlatform V2-01 — Binding / White-label Activation Traceability Gate

**Stage:** V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification  
**Purpose:** prevent a Customer financial resolver or binding migration from activating before its identity and White-label prerequisites are evidenced.

## Decision

Customer financial reads must stay fail-closed until every required identity link is canonical and evidenced. This gate does not create a Tenant, customer-account link, Kimia account, migration, API or write operation.

White-label is a confirmed product requirement. The exact Tenant/Company/Connector/Book runtime model is **not yet grounded** by a currently recovered authoritative source and must not be invented.

## Source-integrity correction

A prior V2-01 documentation pass cited `ADR-024` / `ADR-026` and detailed binding/Tenant rules as accepted Ground Truth. The current recovery pass could not re-establish those exact sources from GitHub, the available Project Memory, Domain Workshop, Kimia audit, or continuation files. Therefore those details are not activation authority.

Current classification for those prior claims: **BLOCKED BY SOURCE RECOVERY**.

This is intentionally different from saying the documents never existed. Under Preserve First and first-search-miss rules, source recovery continues; meanwhile no schema or resolver may rely on them.

## Traceability matrix

| Requirement | Current source | Canonical implementation evidence | Classification | Activation gate |
|---|---|---|---|---|
| Kimia is final authority for Money/Gold/Coin/Currency | current project instructions + canonical recovery | Customer financial controllers fail closed; canonical Kimia read repositories exist | REUSE AS-IS | Never replace with Wallet/Ledger final balance |
| `Account.kimia_id` is exact local Kimia identifier | canonical Account migration/model | `accounts.kimia_id` non-null + unique | REUSE AS-IS | No sample/zero/first-record fallback |
| User may reference local Account | canonical User migration/model | `users.account_id` nullable FK | REUSE AS-IS | Existence of FK is not approval of a runtime binding |
| Registration produces/approves Customer↔Account binding | required V2-01 runtime chain | `RegistrationService` retains TODO and does not assign `account_id` | NOT IMPLEMENTED | Explicit approved binding workflow required; no inference allowed |
| Exact binding cardinality/immutability beyond current schema | prior V2 notes referenced ADR-024, exact source unrecovered | no equivalent canonical enforcement evidenced | BLOCKED BY GROUND TRUTH | Recover authoritative source or obtain explicit owner decision before migration/guard |
| Customer resolver uses exact AccountId, no fallback | V2-01 safety contract | structural relation exists; live resolver absent | NOT IMPLEMENTED | Fail closed on null/orphan/duplicate/ambiguous/unproven scope |
| GoldPlatform is White-label | current project instructions | product requirement confirmed | REUSE AS-IS | Do not equate White-label requirement with an invented Tenant schema |
| Tenant/Company identity model | exact source not recovered | no canonical root evidenced | BLOCKED BY GROUND TRUTH | Exact model and scoping must be established before use |
| Connector/Book routing per White-label context | Kimia evidence shows `X-Book-Id` can matter on some endpoints; exact customer-balance routing rule not established | no canonical Tenant/Connector mapping evidenced | BLOCKED BY GROUND TRUTH | Do not infer global or tenant-specific routing |
| Reconciliation reports conflicts without repair | V2-01 + PR #196 conceptual evidence | canonical reconstructed service/command/test | TESTED — NOT MERGED | Keep read-only |
| Customer balance presentation reads Kimia | canonical recovery + merged Kimia read path | current endpoints 503 pending resolver | REUSE AS-IS | Connect only after verified resolver; no direct Controller→Kimia client |
| Kimia Write | deny-by-default architecture | write foundation exists but production Ground Truth absent | BLOCKED BY GROUND TRUTH | Out of V2-01 activation scope |

## Fail-closed resolver contract — future implementation gate

A future resolver may return an exact Kimia AccountId only when all applicable checks are positively evidenced:

1. authenticated user exists under accepted Auth rules;
2. `users.account_id` was populated by an approved non-inferred binding workflow;
3. referenced local Account exists;
4. local Account has its exact canonical `kimia_id`;
5. binding state is not duplicate, orphaned or ambiguous under the final accepted cardinality rule;
6. any White-label Tenant/Company/Connector/Book context required by the final architecture is resolved by an approved backend-owned mechanism;
7. no sample, first-record, zero, mobile/name/national-code/account-code fallback is used.

Any failed or unproven check must keep the Customer financial path unavailable. Reconciliation may report the conflict but must not repair it.

## Migration gate

No new binding or Tenant/Company/Connector migration is authorized merely because a prior V2 note described one.

Before any migration:

- authoritative rule/source must be recovered or explicitly decided;
- current canonical/runtime schema must be inspected;
- duplicate/orphan preflight must be read-only;
- any historical implementation must be compared, not copied blindly;
- uniqueness/scoping semantics must be established;
- rollback/data-preservation behavior must be documented;
- migration tests and exact-head CI must execute.

Current migration action: **NOT IMPLEMENTED**.

## Current evidence status

Exact head `1b7380abc688b4fea295176ffd759e3396164b71` before this source-integrity correction:

- Backend RC1 Validation #440 — EXECUTED — PASS
- Operational Readiness #50 — EXECUTED — PASS

This proves the read-only reconciliation/documentation state on that exact SHA, not the missing authenticated resolver or any Tenant/Connector design.

## Exit impact

V2-01 cannot close while the authenticated Customer→approved Account→exact Kimia AccountId path remains unimplemented. If White-label context participates in financial identity/routing, that context must also be grounded before activation.

This is not permission to guess missing links. It is an explicit activation blocker.
