# V2-00 — Monitoring/Health overlap and Admin view gap

## Status

`V2-00 — EVIDENCE RECOVERY`

## Sources inspected

- PR #71 — `Stage 16: add operational health and observability baseline` — merged.
- PR #128 — `AP-12: add monitoring and system health read foundation` — closed, not merged, previously classified `SUPERSEDED`.
- Current canonical branch: `recovery/rc2-product-rebuild`.

## Canonical evidence

PR #71 merged operational health into the canonical history. Current canonical contains `backend/app/Support/OperationalHealthService.php` with probes for:

- database;
- Redis;
- storage;
- failed queue jobs;
- outbox;
- Kimia safety (`read_only=true`, `write_enabled=false`).

The canonical operational path also uses `ops:health --json --fail-on-degraded`.

## Historical AP-12 evidence

PR #128 proposed an Admin-facing API endpoint and permission:

- `GET /api/v1/admin/system/health`;
- permission `system-health.view`;
- `AdminSystemHealthReadModel`;
- `AdminSystemHealthReadController`;
- contract tests preventing operator access and sensitive host/password leakage.

However PR #128 was closed without merge and recovery audit classified it as `SUPERSEDED`.

## Duplicate / drift conclusion

The AP-12 backend health probing logic substantially overlaps the already-canonical `OperationalHealthService`. Reintroducing `AdminSystemHealthReadModel` as an independent second health implementation would create a duplicate source of operational truth.

Classification:

- Operational health core: `REUSE AS-IS`.
- Historical AP-12 standalone health read model: `SUPERSEDED` / `DUPLICATE CANDIDATE` if copied directly.
- Admin-facing safe health capability: `NOT IMPLEMENTED` on the inspected canonical path; this remains a product/API gap only if the V2 Admin experience requires it.

## Safe future direction

If an Admin health endpoint is required later, it should adapt/read from the canonical `OperationalHealthService`, with explicit admin permission and redaction tests. It must not rebuild DB/Redis/Queue/Outbox/Kimia health calculations in a parallel ReadModel.

No new endpoint, permission, migration, financial rule, Kimia behavior, or runtime mutation is authorized by this evidence document.

## V2-00 interpretation

This closes the uncertainty about PR #128 reuse: do not resurrect it as-is. Preserve it as historical evidence and reuse only its Admin API/permission/redaction requirements where applicable, implemented against the canonical health service after V2-00 gate closure.
