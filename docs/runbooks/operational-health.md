# Operational Health Runbook

## Command

```bash
php artisan ops:health --json --fail-on-degraded
```

## Components

- database connectivity and latency;
- Redis connectivity and latency;
- storage writability;
- failed queue jobs;
- pending outbox messages;
- Kimia safety state.

## Degraded response

1. preserve the JSON output and correlated logs;
2. do not retry financial operations manually;
3. inspect database/Redis availability;
4. inspect failed jobs and outbox state;
5. confirm `KIMIA_READ_ONLY=true` and `KIMIA_WRITE_ENABLED=false`;
6. escalate before changing financial data or replaying jobs.

## Slow queries

Queries exceeding `SLOW_QUERY_MS` are logged with connection, duration and SQL template. Bindings are intentionally excluded.

## External monitoring boundary

The command is machine-readable and returns a non-zero exit code when degraded. Deployment infrastructure must connect this command to its approved metrics and alerting system; no external provider is assumed in the repository.
