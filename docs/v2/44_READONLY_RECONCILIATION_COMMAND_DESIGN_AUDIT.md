# V2-00 — Read-only Reconciliation Command Design Audit

**Status:** DESIGN AUDIT ONLY — NO COMMAND IMPLEMENTATION

## Purpose

Determine whether the future Customer/Kimia reconciliation report needs a new execution pattern or can reuse existing safe console-command conventions.

## Existing safe command evidence

Canonical currently contains read-only Kimia inspection/validation commands with useful patterns:

### `kimia:validate-read`

- explicit read-only safety check;
- optional machine-readable `--json` output;
- service injection rather than direct HTTP in the command;
- non-zero exit on failed validation;
- avoids exposing credentials/raw sensitive payloads.

### `kimia:inspect-transactions`

- explicit positive integer validation;
- repository injection;
- read-only description and behavior;
- bounded/paginated output;
- no database mutation or Kimia Write.

## Duplicate/reinvention decision

A reconciliation report **does not require a new generic command framework**.

Classification:

- existing console read-only pattern: `REUSE AS-IS`;
- reconciliation-specific query/report service: `IMPLEMENT CANDIDATE`;
- a second generic Kimia inspection framework: `DUPLICATE CANDIDATE — DO NOT CREATE`.

## Recommended future shape

If implementation is later authorized, prefer one narrowly-scoped command such as:

```text
kimia:reconcile-customer-accounts
```

with behavior conceptually equivalent to:

```text
Command
  -> CustomerKimiaReconciliationService
      -> local read-only repositories/queries
      -> reconciliation classifier
      -> immutable report DTO/array
  -> table or --json output
```

The name above is a design placeholder, not an approved public contract.

## Mandatory safety properties

The future command/service MUST:

1. perform SELECT/read operations only;
2. never call `create`, `update`, `delete`, `save`, `updateOrCreate`, `firstOrCreate`, raw DML, or Kimia Write;
3. never mutate `users.account_id`, `accounts`, or `external_accounts`;
4. classify by exact AccountId comparison only;
5. never infer links from mobile, national code, name, or account code;
6. fail closed when future Tenant/Connector context is required but unresolved;
7. offer deterministic machine-readable output for CI/evidence capture;
8. expose counts plus conflict identifiers without credentials or secret configuration;
9. return failure when blocking integrity conflicts are detected if invoked in preflight mode;
10. be safe to rerun without side effects.

## Suggested modes

Design-only recommendation:

```text
--json
--summary-only
--fail-on-conflict
```

Do not add `--fix`, `--link`, `--backfill`, `--sync-write`, or similar mutation flags to the read-only command. Any later mutation workflow must be a separate reviewed capability.

## Report schema

At minimum the machine-readable result should contain:

```text
status
scope_status
counts.accounts
counts.external_kimia_accounts
counts.exact_id_present_both
counts.external_only
counts.account_only
counts.user_account_link_duplicate
counts.user_account_link_orphan
counts.user_bound_account_without_kimia_projection
counts.matched_account_without_user_binding
counts.user_bound_matched_account
blocking_conflicts[]
observed_at
```

No balance amounts are required for this reconciliation stage.

## Tenant/Connector boundary

Current canonical runtime is global and therefore a present-day report may only describe the current dataset as a recovery evidence slice.

It must not claim multi-tenant correctness.

Before any runtime activation, the same classifier must operate inside an explicit:

```text
Tenant + active Kimia Connector/Book
```

scope.

## Tests required before future implementation can be accepted

Minimum test contract:

- exact match classification;
- external-only classification;
- account-only classification;
- duplicate user/account link detection;
- orphan user/account detection;
- matched account without user binding;
- bound matched account;
- no mutation assertion for every participating table;
- JSON output contract;
- `--fail-on-conflict` exit-code behavior;
- architecture guard preventing Kimia Write or model mutation from the reconciliation path.

Until those tests are written and executed, any implementation remains `IMPLEMENTED — NOT TESTED` at best.

## Conclusion

The safe path is to **reuse the current console read-only conventions** and add only the reconciliation-specific classifier/service when implementation is authorized.

No product behavior, schema, Kimia Write, balance authority, or customer resolver is changed by this design audit.
