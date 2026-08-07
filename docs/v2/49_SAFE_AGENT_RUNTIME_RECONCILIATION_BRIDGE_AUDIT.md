# GoldPlatform V2 — Safe Agent Runtime Reconciliation Bridge Audit

Status: V2-00 evidence gate; documentation only
Date: 2026-08-07

## Purpose

Determine whether the existing GitHub Issue driven local Agent can safely execute the already-tested read-only customer-to-Kimia reconciliation against the current shop runtime without asking the owner to manually operate the computer.

This document does not change the Agent allow-list, switch any local branch, run self-update, execute the reconciliation command, mutate database state, activate a connector, run a migration, or perform Kimia Write.

## Re-verified exact-SHA CI before bridge analysis

V2 head before this documentation commit:

`04f4d163ddc7eae5dc6521a55e2dc5474f20efd6`

Exact-SHA workflows:

- Operational Readiness #29 — EXECUTED — PASS
- Backend RC1 Validation #415 — EXECUTED — PASS

## Existing Agent execution model

The existing remote queue is a fixed allow-list executor. It accepts only exact `COMMAND=<allowed-command>` values from owner-authored GitHub issues and does not execute arbitrary issue payloads.

Current allowed commands include:

- `self-update`
- `health-check`
- `tests`
- `docker-status`
- `git-status`
- `kimia-readonly`
- `recent-logs`

The current `kimia-readonly` command executes the existing Kimia connection check and historical AccountId 350 transaction inspection. It does not execute `kimia:inspect-account-reconciliation`.

Therefore an issue containing an unrecognized reconciliation command would be rejected by design.

## Branch/update boundary

The Agent self-update script is branch guarded. In the V2 branch copy it still expects the historical Agent workspace branch:

`feature/goldplatform-developer-mcp`

The same remote queue implementation is present on that historical branch.

The self-update mechanism:

1. verifies repository origin;
2. requires the exact expected branch;
3. refuses a dirty working tree;
4. fetches only the expected branch;
5. permits only fast-forward update;
6. refuses non-fast-forward ancestry;
7. validates Agent PowerShell syntax after update.

This is a useful safety property and must not be bypassed merely to obtain V2 evidence.

## Bridge feasibility

A safe bridge is technically feasible, but it is **not currently deployable without an explicit cross-branch integration step**.

The minimum safe implementation would be a new fixed allow-list command, for example:

`kimia-reconciliation-readonly`

whose only process execution is:

`docker compose exec -T php php artisan kimia:inspect-account-reconciliation --json --no-ansi`

Required safety properties:

- no arbitrary arguments from GitHub Issue body;
- no shell payload execution supplied by the issue;
- no migration;
- no database writes;
- no Kimia Write;
- no credentials in output;
- sanitized reconciliation output only;
- timeout protected;
- exit code captured;
- Agent claim/result recorded in GitHub;
- branch/origin guard preserved.

However, adding that command only to `v2/source-recovery-v2-00` would not make the installed historical Agent receive it, because its self-update path fetches `feature/goldplatform-developer-mcp`.

Changing the installed Agent's expected branch, merging V2 into the historical Agent branch, or copying/cherry-picking the bridge across branches is an integration action and is not inferred or performed automatically under PRESERVE FIRST.

## Decision boundary

The existing Agent therefore cannot currently supply the missing reconciliation evidence as-is.

Two valid future evidence paths remain:

1. **Direct one-time read-only runtime command** on the current shop runtime using the already-tested Artisan reconciliation command.
2. **Controlled Agent bridge integration** that adds one fixed reconciliation allow-list command to the actual deployed Agent branch, followed by its existing guarded self-update mechanism.

Path 2 requires an explicit integration choice because it changes the operational Agent branch/tooling outside the V2 evidence branch. It is not silently performed.

## Current evidence status

- Historical Agent connectivity: VERIFIED
- Historical Agent Issue execution: VERIFIED
- Current reconciliation command implementation: VERIFIED
- Current reconciliation command exact-SHA CI: VERIFIED
- Current shop reconciliation execution: NOT VERIFIED
- Existing Agent ability to run reconciliation: NOT IMPLEMENTED
- Safe bridge design: DOCUMENTED
- Safe bridge deployment: NOT AUTHORIZED / NOT PERFORMED

## Stage decision

`V2-00 — GATE NOT PASSED`

`V2-01 — NOT STARTED`

Customer financial reads remain fail-closed until current-runtime Tenant + Connector + verified Customer binding + Kimia AccountId resolution are established.
