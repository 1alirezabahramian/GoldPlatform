# Service / Kimia Client Boundary

Status: Accepted

## Boundary

Application services must not call `KimiaClient` or Laravel's HTTP facade directly.

Kimia transport, authentication, retry, logging and read/write guards belong inside `App\Integrations\Kimia`.

Application services may depend on approved Kimia repositories.

## Controlled legacy command exceptions

The following existing manual commands still use `KimiaClient` directly and are locked by an explicit architecture-test allowlist:

- `SyncKimiaCoins.php`
- `SyncKimiaCurrencies.php`
- `TestKimiaConnection.php`

These are manual read/synchronization or diagnostic tools. No new command may bypass the repository boundary silently.

Migration of these legacy commands to repositories is a separate controlled task and must preserve their verified behavior.

## Safety

- No Kimia Write was enabled.
- No financial rule, Action Code or payload was introduced.
- No migration or production behavior was changed.
- Unrelated HTTP providers, including SMS transport, are outside this Kimia-specific boundary.
- This boundary is enforced by `ServiceKimiaClientBoundaryTest`.
