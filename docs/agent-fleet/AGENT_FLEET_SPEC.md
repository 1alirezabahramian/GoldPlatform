# GoldPlatform Agent Fleet Specification

Status: Draft v0.1
Owner: Alireza Bahramian
Scope: Private development and operational agents for GoldPlatform

## Principles

- Deny by default.
- Least privilege per agent.
- No shared unrestricted credential.
- No secret, token, cookie, password, private key, customer PII, or Kimia credential in Git, issue bodies, CI logs, screenshots, or generated documentation.
- Every action must have agent identity, correlation id, timestamp, target, branch/environment, result, and audit record.
- Financial truth remains Ledger plus confirmed documents. Agents must not directly mutate balances.
- Kimia write remains disabled until an operation, payload, transaction mapping, idempotency rule, compensation rule, and approval gate are explicitly confirmed.
- Competitor or external-product research is limited to environments the owner is authorized to access, using normal user flows only. No bypass, credential extraction, hidden endpoint abuse, destructive testing, or terms-of-service circumvention.

## Control Plane

### GoldCommander

Role: Orchestrator and policy enforcement only.

Allowed:
- Route approved jobs to specialized agents.
- Validate branch, environment, timeout, allowlist, and approval requirements.
- Aggregate reports.
- Trigger global kill switch.

Denied:
- Direct code modification.
- Direct Kimia access.
- Direct browser interaction.
- Reading secret values.

## Specialized Agents

### 1. GoldObserver

Purpose: Read-only project inspection.

Allowed:
- Git status, branch, commit, diff, log, PR and CI status.
- Read repository files and approved documentation.
- Docker status and read-only health commands.
- Read non-sensitive application logs with masking.

Denied:
- File modification.
- Git commit, push, merge or branch deletion.
- Database writes.

### 2. GoldBuilder

Purpose: Controlled code implementation.

Allowed:
- Modify code only on approved `work/*` branches.
- Run formatter, static checks and approved tests.
- Create commits and push the current approved branch.
- Open draft PRs.

Denied:
- Direct writes to protected branches.
- Merge without green CI and policy approval.
- Editing `.env`, secrets, production data or Kimia credentials.
- Financial rule invention.

### 3. GoldQA

Purpose: Test, regression and evidence generation.

Allowed:
- Unit, feature, financial, ledger, order, settlement, custody, delivery, permission and Kimia mock tests.
- Migration fresh only on disposable databases.
- Production-like Docker validation.
- Generate test evidence and failure reports.

Denied:
- Production database migration.
- Changing code except test-only diagnostic branches explicitly approved.

### 4. GoldPerformance

Purpose: Performance analysis.

Allowed:
- Query count, N+1 detection, explain plans, index review.
- Load, stress and concurrency tests on approved non-production targets.
- Performance baselines and regression reports.

Denied:
- Unbounded load.
- Production stress testing without explicit window and approval.
- Caching financial truth without confirmed invalidation policy.

### 5. GoldMigrationGuard

Purpose: Migration safety.

Allowed:
- Inspect migrations and schema diffs.
- Run migrate:fresh and rollback drills on disposable databases.
- Detect destructive or non-reversible changes.

Denied:
- Production migration execution.
- Table/column deletion without explicit approved change record.

### 6. GoldDocs

Purpose: Documentation synchronization.

Allowed:
- Compare code, tests, ADRs, PROJECT_STATE, CHANGELOG and project memory.
- Create documentation drafts on an approved branch.
- Report contradictions and stale sections.

Denied:
- Recording unverified assumptions as facts.
- Silent reconciliation of conflicting business rules.

### 7. GoldDrift

Purpose: Detect divergence between implementation and approved documentation.

Allowed:
- Compare routes, entities, migrations, configuration, tests, DTOs and documented contracts.
- Open issues with evidence and severity.

Denied:
- Auto-correcting financial or Kimia rules.

### 8. KimiaSentinel

Purpose: Kimia read-only observation and reconciliation.

Allowed:
- Approved Kimia read API endpoints.
- Approved read-only reports and screens in Kimia software.
- Capture masked evidence, schema samples and response metadata.
- Compare Kimia output with GoldPlatform Order, Ledger, Settlement and Custody records.

Denied:
- POST, PUT, PATCH, DELETE.
- Clicking create, save, approve, delete, void, edit or post-document controls.
- Storing credentials, session cookies or unmasked customer data.

Safety:
- Separate read-only Kimia account where supported.
- Endpoint, screen and report allowlist.
- Immediate stop on unknown dialog, changed UI, unknown response schema or authentication anomaly.

### 9. KimiaDraftOperator

Status: Disabled by default.

Purpose: Future preparation of write-operation drafts only.

Allowed when explicitly enabled:
- Build a proposed payload from approved mappings.
- Validate idempotency, payload hash and compensation reference.
- Present dry-run impact for human approval.

Denied:
- Executing the operation.

### 10. KhalifehUXResearcher

Purpose: Authorized product-flow research for `app.khalifehcoin.com` and `admin.khalifehcoin.com`.

Allowed:
- Normal authenticated navigation using owner-provided authorized accounts.
- Capture screenshots, steps, field names, validation messages and workflow timing.
- Compare customer/admin journeys with GoldPlatform requirements.
- Produce UX and functional gap reports.

Denied:
- Bypassing authentication or authorization.
- Accessing other users' data.
- Hidden endpoint probing, scraping beyond normal use, destructive tests or credential extraction.
- Automated submission of financial transactions unless a dedicated sandbox and explicit test case are approved.

### 11. GoldRelease

Purpose: Release candidate and deployment gate.

Allowed:
- Aggregate CI results.
- Validate production checklist, config guard, backup/restore evidence, deployment and restart tests.
- Create release candidate notes and tags after approval.

Denied:
- Production deployment without explicit target, maintenance plan, rollback plan and owner approval.

### 12. GoldSecurity

Purpose: Defensive security checks.

Allowed:
- Dependency audit, secret scan, configuration review, security-header tests and permission tests.
- Report findings and create remediation branches.

Denied:
- Offensive testing against external or production systems without explicit written scope.

## Permission Tiers

- Tier 0: Observe only.
- Tier 1: Generate reports and issues.
- Tier 2: Modify files on isolated work branches.
- Tier 3: Commit and push approved work branches.
- Tier 4: Open PR and trigger CI.
- Tier 5: Merge only after required checks and policy gate.
- Tier 6: External system write; disabled globally until separately approved per system and operation.

## Mandatory Job Envelope

Every queued job must include:

- `job_id`
- `agent_name`
- `requested_by`
- `repository`
- `branch`
- `environment`
- `operation`
- `allowlist_profile`
- `timeout_seconds`
- `requires_approval`
- `correlation_id`
- `created_at`

Unknown or missing fields cause rejection.

## Windows Installation Model

- One Windows service: `GoldPlatformAgentHost`.
- Specialized agents are policy profiles, not separate unrestricted Windows services.
- Each profile has its own command allowlist, workspace, timeout, log channel and optional browser/API adapter.
- The host runs as a dedicated Windows user with no local administrator rights by default.
- Elevation is a separate one-time approved operation.
- A global kill switch disables queue consumption.
- Heartbeat reports version, machine id alias, active profile, last job, health and queue age; never secret values.

## Browser Automation

- Use a dedicated browser profile created for agents.
- No reuse of the owner's personal Chrome profile.
- Credentials remain in Windows Credential Manager or an approved secret store.
- Screenshots and logs must mask customer identifiers and financial details unless stored in an explicitly protected evidence location.
- Unknown downloads, browser extensions and certificate changes are blocked.

## Installation Gate

Installation on the shop computer is permitted only after:

1. Current Agent Host heartbeat is verified.
2. Host version and repository branch are verified.
3. Backup of current agent configuration is created.
4. Agent Fleet manifest passes schema validation.
5. All profiles pass deny-by-default tests.
6. Windows service install/update script passes dry-run.
7. Rollback script is verified.
8. Owner receives a concise permission matrix.

## Phase Plan

### Phase 1 — Foundation
- Fleet manifest and schema.
- GoldCommander, GoldObserver, GoldBuilder, GoldQA and GoldDocs.
- Audit log, heartbeat and kill switch.

### Phase 2 — Engineering Specialization
- GoldPerformance, GoldMigrationGuard, GoldDrift, GoldSecurity and GoldRelease.

### Phase 3 — Kimia Read-only
- KimiaSentinel API adapter.
- Kimia software read-only browser/desktop adapter.
- Reconciliation reports.

### Phase 4 — Authorized Product Research
- KhalifehUXResearcher for approved app/admin environments.

### Phase 5 — Controlled Draft Operations
- KimiaDraftOperator only after confirmed contracts and explicit approval.

## Acceptance Criteria

- Each agent rejects operations outside its allowlist.
- No agent can read secret values from repository or logs.
- Builder cannot write protected branches.
- Observer cannot modify files.
- KimiaSentinel cannot issue write requests or activate write controls.
- Browser agent cannot access unapproved domains.
- Every action is auditable and correlated.
- Installation and rollback are repeatable.
- Documentation and tests are updated in the same PR.
