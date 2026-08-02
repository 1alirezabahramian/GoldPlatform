# GoldPlatform — Backend Continuous Integration

**Status:** Workflow prepared; first GitHub run pending

**Date:** 2026-08-03

## Purpose

The workflow in [`.github/workflows/backend-tests.yml`](../../.github/workflows/backend-tests.yml)
runs the Laravel automated suite on GitHub whenever Backend code or the workflow itself is
changed on a pull request or on the configured branches.

It complements, but does not replace, the shop verification report:

| Check | GitHub CI | Shop verification |
|---|---:|---:|
| PHP/Laravel automated suite with SQLite in memory | Yes | Yes |
| Real MySQL/Docker composition | No | Yes |
| Real Redis/Nginx runtime | No | Yes |
| Live Kimia read-only evidence | No | Optional controlled step |
| Kimia write | No | No |

## Safety contract

- The workflow receives no Kimia, SMS, Jibit, database, or customer Secret.
- The test database is temporary SQLite memory.
- `KIMIA_WRITES_ENABLED` is explicitly false.
- External integrations must be mocked by tests; CI must not call live Kimia.
- Workflow permissions are read-only for repository contents.
- A newer run cancels an older run for the same branch.

## Trigger scope

The workflow runs for changes under `backend/**` or to the workflow file itself:

- pull requests;
- pushes to `main`;
- pushes to `work/**`;
- pushes to `audit/**`.

Documentation-only changes do not consume a Backend test run.

## Verification status

Local checks can validate YAML structure and referenced project paths. The workflow is not
considered operational until its first GitHub Actions run completes successfully. A CI
failure must be investigated; it must not be described as a shop-runtime failure without
evidence.
