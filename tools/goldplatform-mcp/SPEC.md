# GoldPlatform Developer MCP — Specification

**Status:** Draft v0.1 — approved for initial implementation

**Owner:** Alireza Bahramian

**Repository:** `1alirezabahramian/GoldPlatform`

**Branch:** `feature/goldplatform-developer-mcp`

## 1. Purpose

GoldPlatform Developer MCP is a private developer app that exposes a controlled set of project tools to ChatGPT/Codex through an MCP server.

Its purpose is to let the assistant inspect, modify, validate, and report on the GoldPlatform repository without requiring Alireza to manually type routine commands on the shop computer.

This MCP is a development tool. It is not part of the customer-facing GoldPlatform product and must not contain customer UI or financial business logic.

## 2. Project rules inherited from GoldPlatform

The MCP must follow `docs/00_PROJECT_MEMORY.md` and all accepted ADRs.

Golden rule:

> NO GUESSING — NO REINVENTING — NO SILENT CHANGES

The MCP must never invent:

- Kimia behavior or request contracts
- financial formulas or transaction codes
- wallet rules
- dynamic coin, currency, product, account, or group identifiers
- unapproved architecture changes

When trusted sources conflict, tools must return a structured conflict result and stop before implementation.

## 3. Primary archetype

Initial archetype: **tool-only private MCP app**.

No custom widget UI is required in v0.1. A small operational dashboard may be added later only if it materially improves review, approvals, or test evidence.

## 4. Users

- Human owner: Alireza Bahramian
- AI operator: ChatGPT/Codex
- Runtime host: the Windows shop computer that contains the approved GoldPlatform working copy

## 5. Runtime boundary

Approved project root:

`C:\Users\USER\Desktop\p\GoldPlatform`

The MCP may read and modify files only inside this project root unless a future SPEC revision explicitly permits another path.

Secrets must remain in environment variables or approved local secret storage. Tools must never return or log passwords, tokens, Basic Auth headers, API keys, or `.env` values.

## 6. Version 0.1 scope

### 6.1 Read-only tools

1. `project_status`
   - Git branch, commit, clean/dirty status, Docker Compose status, Laravel version, migration status summary.
   - Annotation: read-only.

2. `read_project_file`
   - Read a UTF-8 project file by repository-relative path.
   - Reject paths outside the project root.
   - Annotation: read-only.

3. `search_project`
   - Search filenames and text inside the project.
   - Exclude `.git`, `vendor`, `node_modules`, secrets, and generated artifacts by default.
   - Annotation: read-only.

4. `run_tests`
   - Run an approved test target: `laravel`, `kimia`, `frontend`, or `all`.
   - Return exit code, duration, concise output, and report path.
   - Annotation: read-only from a business-data perspective; may create test caches/logs.

5. `read_logs`
   - Read bounded recent project logs with secret redaction.
   - Annotation: read-only.

6. `kimia_readonly`
   - Execute only approved read-only Kimia commands already implemented in the repository.
   - Initial operations: connection test, account/group/coin/currency sync dry inspection, balance inspection, transaction inspection.
   - Must not create, update, or delete a Kimia voucher.
   - Annotation: read-only and open-world.

### 6.2 Mutating project tools

7. `apply_patch`
   - Apply a unified diff limited to the project root.
   - Validate paths and reject binary patches, secrets, traversal, and out-of-root changes.
   - Return changed files and diff summary.
   - Annotation: mutating, idempotent only when the same patch is already applied.

8. `write_project_file`
   - Create or replace one UTF-8 text file inside the project root.
   - Must require the complete target path and content hash.
   - Return old/new hashes and diff summary.
   - Annotation: mutating.

9. `run_project_command`
   - Run only named command profiles, not arbitrary shell text in v0.1.
   - Initial profiles: `git-status`, `docker-up`, `docker-ps`, `artisan-about`, `artisan-route-list`, `artisan-migrate-status`, `artisan-test`, `composer-validate`, `npm-build`.
   - Annotation depends on profile; destructive commands are excluded.

10. `git_commit`
    - Commit already-reviewed working-tree changes on the current feature branch.
    - Must refuse on protected branches (`main`, `master`) and when tests required by the request have not passed in the same operation chain.
    - Must not push automatically.
    - Annotation: mutating.

11. `git_push`
    - Push the current feature branch to `origin`.
    - Must refuse protected branches unless a future SPEC revision explicitly allows them.
    - Return remote branch and commit SHA.
    - Annotation: mutating and open-world.

## 7. Explicitly excluded from v0.1

- Arbitrary unrestricted PowerShell or shell execution
- Disk, registry, Windows account, service, firewall, shutdown, restart, or formatting operations
- Access outside the GoldPlatform project root
- Reading or returning `.env` or credentials
- Direct database mutation outside application migrations/tests
- Destructive migrations or `migrate:fresh`
- Kimia voucher creation, update, adjustment, transfer, exchange, trade, or deletion
- Automatic merge to `main`
- Force push
- Deleting unrelated user files
- Chrome/VS Code GUI automation
- Public marketplace submission

These capabilities require a separate SPEC update and explicit approval.

## 8. Approval policy

Safe inspections and non-destructive validation may run without additional approval after the user asks to review, test, diagnose, build, or fix.

The following require explicit user confirmation in the active conversation unless already included in the user's current request:

- committing
- pushing
- creating a pull request
- running migrations that change the development database
- deleting or renaming existing project files
- any Kimia write operation
- any material architecture or financial-rule change

## 9. Tool result contract

Every tool result must include:

- `ok`: boolean
- `operation`: stable tool operation name
- `summary`: concise Persian-ready summary
- `exitCode`: integer or null
- `durationMs`: integer
- `changedFiles`: array
- `warnings`: array
- `evidence`: bounded structured details
- `reportPath`: local relative report path when applicable

Errors must be structured and must not silently become empty success results.

## 10. Audit and traceability

Each mutating call must record a local JSONL audit event under:

`storage/agent-reports/mcp-audit.jsonl`

Audit fields:

- timestamp
- tool name
- sanitized inputs
- result
- changed files
- git branch and SHA before/after
- test evidence
- caller/session identifier when available

Secrets and full file contents must not be written to the audit log.

## 11. Idempotency and concurrency

- Use a single-operation lock for mutating tools.
- Detect an already-applied patch before changing files.
- Do not run simultaneous Git writes.
- Reuse the same operation identifier during retries.
- Return a clear busy/conflict response instead of corrupting the worktree.

## 12. Technology decision for v0.1

- Language: TypeScript on Node.js
- Transport: remote MCP over HTTP at `/mcp`
- SDK: current official MCP/Apps SDK packages selected from current documentation
- Process execution: explicit argument arrays, no shell interpolation by default
- Validation: schema validation for every tool input
- Local host: Windows shop computer
- Public reachability during development: temporary HTTPS tunnel

Package versions must be selected from current official documentation at implementation time and pinned in the lockfile.

## 13. Repository shape

```text
tools/goldplatform-mcp/
├── SPEC.md
├── README.md
├── package.json
├── tsconfig.json
├── src/
│   ├── server.ts
│   ├── config.ts
│   ├── tools/
│   ├── services/
│   ├── security/
│   └── types/
└── tests/
```

## 14. Local development flow

1. Install dependencies.
2. Start the MCP server locally.
3. Confirm `/mcp` is reachable.
4. Validate tool descriptors in MCP Inspector/DevTools.
5. Expose the local port through an HTTPS tunnel.
6. Add the remote MCP URL to ChatGPT Developer Mode.
7. Refresh the app after tool metadata changes.
8. Run a read-only end-to-end test before enabling mutating tools.

## 15. Delivery phases

### Phase A — foundation

- scaffold server
- configuration and path confinement
- audit logger
- `project_status`
- `read_project_file`
- `search_project`
- tests and documentation

### Phase B — validation tools

- `run_tests`
- `read_logs`
- approved command profiles
- Kimia read-only wrapper

### Phase C — controlled writes

- `apply_patch`
- `write_project_file`
- diff evidence
- rollback safety

### Phase D — Git delivery

- `git_commit`
- `git_push`
- optional pull-request creation after explicit approval

### Phase E — later, separately approved

- Playwright and browser testing
- visual evidence
- safe database inspection
- Kimia mutation tools with persisted idempotency and approval gates

## 16. Acceptance criteria for v0.1

The first usable release is accepted only when:

1. The server exposes a reachable `/mcp` endpoint.
2. ChatGPT can discover the documented tools.
3. Path traversal and out-of-project access tests pass.
4. Secret-redaction tests pass.
5. Read-only project status works on the shop computer.
6. Laravel tests can be invoked and their real exit status is returned.
7. A controlled text-file change can be applied on a feature branch with a diff.
8. Audit records are created for mutations.
9. Protected-branch commit/push attempts are refused.
10. README and this SPEC match the actual implementation.

## 17. Current first implementation task

Implement **Phase A only**. Do not add arbitrary command execution, Git write tools, browser automation, or Kimia mutation in the first commit.
