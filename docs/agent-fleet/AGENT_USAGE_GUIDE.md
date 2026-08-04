# GoldPlatform Agent Fleet — Usage Guide

Status: Draft v0.1
Owner: Alireza Bahramian

## Selection rule

Use the least-privileged agent that can complete the task. GoldCommander routes work but does not perform privileged actions itself.

## Agent usage

### GoldObserver
Use for repository status, branch/commit inspection, Docker status, health checks and masked logs. Never use for modifications.

### GoldBuilder
Use for controlled implementation on an approved `work/*` branch. It may edit code, run approved checks, commit and push that branch, and open a draft PR. It may not write protected branches or invent financial/Kimia rules.

### GoldQA
Use after implementation for unit, feature, financial, ledger, order, settlement, custody, delivery, permission and Kimia mock tests. Destructive database tests are restricted to disposable databases.

### GoldPerformance
Use for query budgets, N+1 detection, index review, bounded load, stress and concurrency tests on approved non-production targets.

### GoldMigrationGuard
Use before every migration PR. It checks reversibility, destructive changes, schema drift and rollback on disposable databases. It never runs production migrations.

### GoldDocs
Use at the end of every stage to update PROJECT_STATE, CHANGELOG, ADRs, project memory and related backend/frontend documentation. Unverified claims must be marked as draft or blocked.

### GoldDrift
Use periodically and before release candidates to compare code, routes, migrations, DTOs, tests and approved documentation. It reports contradictions but never silently changes financial rules.

### GoldSecurity
Use for dependency audit, secret scan, security headers, permission tests and configuration review. External offensive testing requires separate written scope.

### GoldRelease
Use only after all implementation and test agents have passed. It aggregates evidence, validates deployment/restart/backup/rollback checklists, and prepares a release candidate. Production deployment remains separately approved.

### KimiaSentinel
Use for approved read-only Kimia API calls and approved read-only screens/reports. It may reconcile Kimia with Order, Ledger, Settlement and Custody. It must stop on unknown dialogs, schema changes or authentication anomalies.

### KimiaDraftOperator
Disabled by default. Future use is limited to producing a proposed write payload, idempotency key, payload hash, compensation reference and dry-run impact. It may never execute a write without a separately approved implementation and owner gate.

### KhalifehUXResearcher
Use only with owner-authorized accounts for normal navigation of `app.khalifehcoin.com` and `admin.khalifehcoin.com`. It may capture flow steps, screenshots, validations and timing. It may not bypass authorization, access other users' data, probe hidden endpoints or submit real financial transactions.

## Mandatory workflow

1. GoldObserver reads current state.
2. GoldCommander selects the lowest required permission profile.
3. GoldBuilder implements only on an approved work branch when code changes are needed.
4. GoldMigrationGuard reviews migrations when present.
5. GoldQA and GoldSecurity run mandatory gates.
6. GoldPerformance runs when query, cache or latency behavior can change.
7. GoldDocs synchronizes documentation.
8. GoldDrift checks code/document consistency.
9. GoldRelease validates the final PR and release candidate.

## Persistent project memory

This guide and `AGENT_FLEET_SPEC.md` are the persistent source of truth for agent purpose and usage. Future sessions must read both files before assigning an agent.

## Installation status vocabulary

- Designed: specification and manifest exist.
- Implemented: host/profile code and tests exist.
- Installed: Windows service/profile files are present on the target machine.
- Verified: heartbeat, deny-by-default tests and rollback drill have passed.

Never report Installed or Verified without machine evidence.
