# GoldPlatform — Project State

- Updated: 2026-08-05
- Canonical recovery branch: `recovery/rc2-product-rebuild`
- Canonical SHA before this documentation alignment: `50720724c5315510537da5071499888c0446f264`
- Recovery status: **Canonical reconstruction in progress — historical PR cleanup complete**
- Open pull requests after cleanup: **0**

## Source-of-truth contract

Kimia is the final source of truth for customer financial balances:

- Money
- Gold
- Coin
- Currency

GoldPlatform must not publish an independent or competing final balance for these four asset classes.

Internal Ledger, Journal, Event Store, Idempotency Registry and Balance Projection are permitted only for:

- audit and traceability;
- idempotency;
- intent/result recording;
- order and settlement workflow;
- reconciliation with Kimia vouchers and records;
- detecting incomplete or duplicated operations.

Any internal projection or snapshot must be Kimia-derived, timestamped, rebuildable and reconcilable. In a conflict, Kimia is authoritative.

GoldPlatform is the source of truth for physical Custody / Amanat. Custody must remain separate from financial balances.

## Canonical recovery content

The recovery branch currently preserves and integrates validated foundations for:

1. Business Engine baseline recovery.
2. Customer API contracts and customer read resources.
3. Kimia read integration.
4. Custody and delivery customer read boundaries.
5. Recovery audit evidence.

The latest canonical merge added explicit Customer API resources and did not add a financial rule, Kimia write behavior, migration or balance mutation.

## Historical PR cleanup

Historical and stacked pull requests were inspected against the canonical recovery branch. Unsafe, divergent, duplicated or superseded PRs were closed without merge. Their branches and commits were preserved as historical evidence.

No force push, shared-history rebase, hard reset, broad revert, branch deletion or blind cherry-pick was performed during cleanup.

## Validation status

For canonical SHA `50720724c5315510537da5071499888c0446f264`:

- Tests on the exact final canonical SHA: **NOT EXECUTED / NOT CONFIRMED**
- GitHub Actions on the exact final canonical SHA: **NOT CONFIRMED**
- Production Ready: **NOT CLAIMED**

Earlier CI results and historical test reports remain evidence only; they do not replace a full validation run on the final canonical SHA.

## Kimia safety

- Kimia Read and Kimia Write must remain separate paths.
- Kimia Write remains disabled until real payloads, action codes, transaction codes, account mappings, retry behavior and post-write readback are confirmed from ground truth.
- No Controller may call Kimia Client directly.
- No sample AccountId, ProductId or transaction identifier may be treated as a permanent hard-coded rule.
- Money and weight calculations must use exact Decimal or String Decimal; float is prohibited.

## Remaining recovery gaps

1. Run the full validation matrix on one exact canonical SHA.
2. Audit the canonical code for any internal Money/Gold/Coin/Currency balance treated as final truth.
3. Compare historical evidence with canonical code and reconstruct only genuine missing capabilities through small, isolated PRs.
4. Verify Permission, IDOR, tenant/company/branch isolation and White-label safety.
5. Validate migrations, API/OpenAPI contracts, frontend build/typecheck/E2E where applicable.
6. Align CHANGELOG, ADR status, implementation reports and test reports with the canonical recovery state.
7. Produce final Recovery Closure only after CI is green on the exact closure SHA.

## Current next step

Perform the canonical architecture-drift and capability-gap audit. Do not start a new product stage merely to show progress. Reconstruct only verified missing capabilities, preserve historical evidence and keep Kimia as the final financial balance authority.
