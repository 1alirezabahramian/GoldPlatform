# V2-00 — Customer/Kimia Read-only Reconciliation Specification

**Status:** DESIGN ONLY — NO DATA MUTATION

## Purpose

Define a read-only reconciliation contract between the two currently verified account representations:

- `accounts.kimia_id` — local customer-binding account representation;
- `external_accounts(provider='kimia', external_id)` — current Kimia synchronization projection.

This specification does **not** authorize linking, backfill, migration, tenant activation, connector creation, Kimia Write, or customer balance exposure.

## Grounded constraints

Current verified schema/code shows:

- `accounts.kimia_id` is globally unique today;
- `external_accounts` is globally unique on `(provider, external_id)` today;
- `users.account_id` is the intended local binding path;
- current Kimia account sync writes to `external_accounts`, not `accounts`;
- current canonical runtime has no tenant-owned Kimia connector and no tenant/connector scope on these account rows;
- ADR-026 requires future tenant/connector-scoped uniqueness and explicit tenant/connector context.

Therefore reconciliation may compare identities, but it must not infer durable ownership from names, mobile, national code, or account code.

## Identity key used for read-only comparison

For the current single-connector historical/canonical evidence slice only:

```text
accounts.kimia_id
    ↔
external_accounts.external_id WHERE provider = 'kimia'
```

This equality is a **comparison key only** until tenant and connector scope are introduced and verified.

## Required report buckets

Every relevant row must land in exactly one primary bucket.

### R1 — EXACT_ID_PRESENT_BOTH

One `accounts.kimia_id` equals one Kimia `external_accounts.external_id`.

Meaning:
- identity values agree;
- no automatic link is authorized;
- future Candidate-for-Link status still requires verified tenant + connector context and user-binding checks.

### R2 — EXTERNAL_ONLY

Kimia projection exists in `external_accounts`, but no row exists in `accounts` with the same Kimia identifier.

Meaning:
- discovery/sync exists;
- customer binding does not exist in the local `accounts` representation;
- do not create `accounts` automatically.

### R3 — ACCOUNT_ONLY

An `accounts.kimia_id` exists, but no synchronized Kimia `external_accounts` row currently exists for the same identifier.

Meaning:
- local binding representation may be stale, historical, unsynchronized, or otherwise unresolved;
- do not delete, relink, or replace it automatically;
- requires sync/runtime evidence.

### R4 — DUPLICATE_EXTERNAL_ID_CONFLICT

More than one effective Kimia projection exists for the same future tenant/connector/external identity.

Current global unique constraint should prevent this exact shape in the present schema, but it remains a mandatory preflight category before changing uniqueness or importing/backfilling data.

Action: **BLOCK** mutation.

### R5 — DUPLICATE_ACCOUNT_KIMIA_ID_CONFLICT

More than one local account represents the same future tenant-scoped Kimia AccountId.

Current global `accounts.kimia_id UNIQUE` should prevent this exact shape in the present schema, but it remains a mandatory preflight category before replacing constraints/backfilling.

Action: **BLOCK** mutation.

### R6 — USER_ACCOUNT_LINK_DUPLICATE

More than one User points to the same non-null `users.account_id`.

Historical migration evidence already uses this duplicate preflight before adding one-to-one uniqueness.

Action: **BLOCK** mutation/link activation.

### R7 — USER_ACCOUNT_LINK_ORPHAN

A non-null `users.account_id` cannot resolve to a valid local Account.

Action: **BLOCK** customer financial resolver for the affected User and investigate schema/runtime state.

### R8 — USER_BOUND_ACCOUNT_WITHOUT_KIMIA_PROJECTION

A User is bound to an Account, but the Account's Kimia identifier is not present in current Kimia projection data.

Action: fail closed for customer financial reads until verified.

### R9 — MATCHED_ACCOUNT_WITHOUT_USER_BINDING

`accounts` and `external_accounts` agree on AccountId, but no User is bound to that Account.

Meaning:
- this can be a reconciliation-clean external/local account pair;
- it is **not** automatically a customer-link candidate.

### R10 — USER_BOUND_MATCHED_ACCOUNT

User → Account is present and the AccountId is also present in the Kimia projection.

This is the strongest current read-only structural match, but customer balance exposure still requires:

1. verified tenant ownership;
2. verified active connector/book;
3. tenant/user cross-check;
4. immutable binding checks;
5. Kimia read success;
6. fail-closed behavior on stale/unavailable evidence.

## Forbidden matching heuristics

The reconciliation report MUST NOT classify or link by:

- mobile;
- national code / national_id;
- name;
- shop name;
- account code;
- fuzzy text similarity;
- positional/order coincidence;
- sample IDs.

Those fields may be displayed only as **diagnostic context** after the AccountId-based bucket is determined.

## Minimum read-only queries/checks

Implementation may use SQL, Eloquent, or a report command, but must produce equivalent counts and row-level evidence for:

1. total `accounts` count;
2. total Kimia `external_accounts` count;
3. `EXACT_ID_PRESENT_BOTH` count;
4. `EXTERNAL_ONLY` count;
5. `ACCOUNT_ONLY` count;
6. duplicate non-null `users.account_id` groups;
7. user/account orphan count;
8. users bound to accounts whose Kimia ID has no current projection;
9. matched account pairs with no User binding;
10. matched account pairs with exactly one User binding.

All output must be read-only and safe to rerun.

## Preflight invariants before any later mutation

A later migration/linking checkpoint must not proceed unless all are true for the bounded pilot dataset:

```text
unresolved duplicate user→account links = 0
orphan users.account_id = 0
duplicate future tenant/connector external identities = 0
duplicate future tenant-scoped AccountId identities = 0
unclassified rows = 0
```

Additionally:

- pilot Tenant identity must be explicit, not an implicit default;
- active Kimia connector/book identity must be explicit;
- pre/post backfill row counts must reconcile;
- no financial balance values are copied from Wallet/Ledger as Kimia truth;
- no Kimia Write is performed.

## Candidate-for-Link classification

A row may be labelled only **CANDIDATE FOR EXPLICIT LINK REVIEW** when all of the following are proven:

- exact Kimia AccountId match;
- same explicit Tenant;
- same explicit active Kimia Connector/Book;
- local Account is not already bound to another User;
- target User is not already bound to another Account;
- no duplicate/orphan conflict exists;
- requested link is based on explicit verified ownership evidence, not identity-field similarity.

Even then, the report itself performs no link.

## Safety classifications

| Area | Classification |
|---|---|
| read-only reconciliation report | `REUSE/IMPLEMENT CANDIDATE` |
| automatic Account creation from ExternalAccount | `NOT APPROVED` |
| automatic User linking | `NOT APPROVED` |
| fuzzy/mobile/national-code matching | `NOT APPROVED` |
| tenant/connector-scoped reconciliation | `REQUIRED BEFORE RUNTIME ACTIVATION` |
| customer balances before verified resolver | `FAIL-CLOSED` |
| Kimia Write | `BLOCKED BY GROUND TRUTH` |

## Next safe checkpoint

The next safe implementation slice is a **read-only reconciliation command/report design audit** against current command conventions and CI boundaries. It may calculate/report counts only. It must not create/update/delete database rows and must not activate tenancy or Kimia Write.
