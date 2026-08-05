# Phase 0 — Reconstruction Base Decision

> Status: Accepted for reconstruction planning
>
> Date: 2026-08-05
>
> Recovery branch: `recovery/phase-0-current-state`

## Decision

The technical parent for every clean reconstruction slice is:

- Branch: `main`
- Recovery-start SHA: `31d55fac545201c7b436e940e48e9dcd89bd553d`

This does **not** mean `main` is a complete product baseline. It means `main` is the least-contaminated accepted parent containing the merged Business Engine Stage 00, Stage 01 and Stage 02 work.

Historical product branches and merged commits remain preservation/evidence sources, not direct integration bases.

## Why `main` is selected as parent

1. Stage 00, Stage 01 and Stage 02 are already merged there.
2. The Kimia read-only foundation and financial kernel are present there.
3. Starting from a historical product branch would omit or conflict with the accepted Stage 00–02 line.
4. AP, OP, Customer and Stage 03 branches are substantially diverged and contain unrelated history.
5. A clean parent allows each recovered capability to be reviewed, tested and reverted independently.

## Why historical branches are not selected

### `feature/goldplatform-developer-mcp`

This line contains valuable Customer, Custody, Delivery, Settlement, production, documentation and integration work, but it is hundreds of commits diverged from current `main` and contains multiple parallel architecture generations.

Classification: **Historical Donor / Evidence Source**.

### Customer Closure commit `5da4da919...`

The closure is preserved as accepted historical evidence for the Customer Platform work, but it is not a descendant of current `main`.

Classification: **Accepted Closure / Diverged Implementation Donor**.

### Stage 03 branch

Contains a coherent Quote/Order trading slice, but it uses an Order lifecycle that is not yet reconciled with the existing product Order model and Customer API history.

Classification: **Clean Reconstruction Required**.

### AP and OP chains

Contain valuable permission, operational read, session bootstrap and frontend work, but their branches contain broad historical product changes.

Classification: **Capability Donors / Not Integration Bases**.

## Reconstruction policy

Every capability will use this process:

1. Create a clean branch from current accepted `main`.
2. Identify one bounded capability.
3. Inspect exact donor files and dependencies.
4. Compare with existing `main` equivalents.
5. Rebuild or port only the missing behavior.
6. Add matching tests and documentation.
7. Run migration, contract, permission, tenant and regression checks.
8. Open one clean PR to `main`.
9. Merge only after exact-head CI passes.

No direct merge, rebase, force-push or blind cherry-pick of historical chains is permitted.

## Current permission foundation evidence

`main` already requires `spatie/laravel-permission`, but the current `User` model does not use `HasRoles`, and the default `DatabaseSeeder` creates only a test user. Therefore the package dependency exists, while the application-level permission foundation is incomplete on `main`.

This makes the first low-risk reconstruction candidate:

- non-destructive canonical permission catalog;
- `HasRoles` integration;
- safe, idempotent seeding;
- middleware alias verification;
- tests proving existing roles/direct permissions are preserved.

No permission for Kimia Write, balance mutation or unverified financial action may be introduced.

## CI limitation

The existing Business Engine workflow triggers on backend or workflow file changes. The Recovery PR currently changes documentation only, so the absence of a run on the Recovery head is expected and is not a PASS.

The first reconstructed backend slice must trigger the full baseline workflow and establish exact-head test evidence.

## Status

- Reconstruction parent decision: **Accepted**
- Complete product baseline: **Not Yet Reconstructed**
- Exact-SHA regression: **NOT EXECUTED on reconstruction slice**
- First implementation slice: **Canonical Permission Foundation**
