# GoldPlatform V2-01 — Binding / Tenant Activation Traceability Gate

**Stage:** V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification  
**Purpose:** prevent a Customer financial resolver or binding migration from activating before its identity and Tenant prerequisites are evidenced.

## Decision

Customer financial reads must stay fail-closed until every required identity link below is canonical and evidenced. This gate does not create a Tenant, customer-account link, Kimia account, migration, API or write operation.

## Traceability matrix

| Requirement | Source / Ground Truth | Canonical implementation evidence | Current classification | Activation gate |
|---|---|---|---|---|
| Kimia is final authority for Money/Gold/Coin/Currency | Accepted architecture / Project Memory / canonical recovery | Customer financial controllers fail closed; canonical Kimia read repositories exist | REUSE AS-IS | Never replace with Wallet/Ledger final balance |
| One login has zero/one local Account and zero/one Kimia AccountId | Accepted binding rule / ADR-024 reference | `users.account_id` nullable FK + `accounts.kimia_id` unique | REUSE AFTER FIX | Canonical enforcement and approved link source required |
| One Kimia AccountId cannot be shared by multiple logins | Accepted binding rule | `accounts.kimia_id` unique, but `users.account_id` not unique | REUSE AFTER FIX | Duplicate preflight + tenant-safe canonical constraint design required before migration |
| Established Kimia binding is immutable | Accepted binding rule; historical prepared guards | No equivalent canonical guard evidenced in reviewed runtime | HISTORICAL ONLY | Reconstruct and test guard only after canonical identity scope is known |
| Registration produces/approves Customer↔Account binding | Required V2-01 runtime chain | Canonical `RegistrationService` retains TODO and does not assign `account_id` | NOT IMPLEMENTED | An explicit approved binding workflow is required; no inference allowed |
| Customer resolver uses exact AccountId, no fallback | V2-01 safety contract | Structural relation exists; live resolver absent | NOT IMPLEMENTED | Must fail closed on null, orphan, duplicate, ambiguous or scope mismatch |
| GoldPlatform is White-label / Multi-tenant | Accepted product direction / ADR-026 reference | No canonical Tenant root evidenced | NOT IMPLEMENTED | Canonical Tenant identity boundary required before resolver activation |
| Authenticated user belongs to resolved Host Tenant | Accepted Tenant direction | No canonical Host Tenant/user equality runtime evidenced | NOT IMPLEMENTED | Must be backend-enforced; client cannot select tenant |
| Kimia connector/book belongs to Tenant | Accepted Tenant direction | No canonical Tenant→Kimia connector runtime mapping evidenced | BLOCKED BY GROUND TRUTH | Exact connector/book identity required before financial read routing |
| Reconciliation reports conflicts without repair | V2-01 + PR #196 conceptual evidence | Canonical reconstructed service/command/test | TESTED — NOT MERGED | Keep read-only; exact-head CI required for each changed slice |
| Customer balance presentation reads Kimia | Canonical recovery + merged Kimia read path | Current endpoints 503 pending resolver | REUSE AS-IS | Connect only after verified resolver; no direct Controller→Kimia client |
| Kimia Write | Deny-by-default architecture | Write foundation exists but production registry lacks Ground Truth | BLOCKED BY GROUND TRUTH | Out of V2-01 activation scope |

## Fail-closed resolver contract — future implementation gate

A future resolver may return an exact Kimia AccountId only when all applicable checks are positively evidenced:

1. authenticated user exists and is active under accepted Auth rules;
2. Tenant context is resolved by an approved backend-owned mechanism;
3. user belongs to that Tenant;
4. `users.account_id` is explicitly populated by an approved binding workflow;
5. the referenced local Account exists;
6. the local Account has its exact canonical Kimia identifier;
7. the binding is not duplicate, orphaned or ambiguous;
8. the Tenant's exact Kimia connector/book context is resolved when required;
9. no sample, first-record, zero, mobile/name/national-code/account-code fallback is used.

Any failed or unproven check must keep the Customer financial path unavailable. Reconciliation may report the conflict but must not repair it.

## Migration gate

No new binding/Tenant migration is authorized merely because the accepted rule is known. Before any migration:

- current production/canonical schema state must be inspected;
- duplicate/orphan preflight must be read-only;
- historical prepared migrations must be compared, not copied blindly;
- Tenant-scoped uniqueness semantics must be established;
- rollback/data-preservation behavior must be documented;
- migration tests and exact-head CI must execute.

Current migration action: **NOT IMPLEMENTED**.

## Historical reuse classification

- Prepared nullable-unique `users.account_id` migration: **HISTORICAL ONLY**, candidate **REUSE AFTER FIX**.
- Prepared User/Account/ExternalAccount immutability guards: **HISTORICAL ONLY**, candidate **REUSE AFTER FIX**.
- Historical PR #131 Tenant discovery: **HISTORICAL ONLY**; it documented absence rather than supplying canonical Tenant runtime.
- Historical stacked tenant-related implementation must not be merged/cherry-picked blindly.

## Current V2-01 validation point

Reconciliation exact head before this documentation gate:

`058680093fea90a30235acc1171c744a3c472ca1`

- Backend RC1 Validation #437 — EXECUTED — PASS
- Operational Readiness #47 — EXECUTED — PASS

This proves the current read-only reconciliation slice, not the missing Tenant/binding resolver.

## Exit impact

V2-01 cannot close while the authenticated Customer→verified Account→Tenant/connector-aware Kimia AccountId chain remains unimplemented. This is not permission to guess the missing links; it is an explicit activation blocker.
