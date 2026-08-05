# Service / Kimia Client Boundary

Status: Accepted

## Boundary

Application services and console commands must not call `KimiaClient` or Laravel's HTTP facade directly.

Kimia transport, authentication, retry, logging and read/write guards belong inside `App\Integrations\Kimia`.

Application services may depend on approved Kimia repositories. Manual read/synchronization commands may also use those repositories, but must not bypass them to call the client or raw HTTP transport.

## Safety

- No Kimia Write was enabled.
- No financial rule, Action Code or payload was introduced.
- No migration or production behavior was changed.
- This boundary is enforced by `ServiceKimiaClientBoundaryTest`.
