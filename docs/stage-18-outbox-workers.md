# Stage 18 — Outbox Dispatcher and Workers

Implemented:

- deny-by-default event handler registry;
- row-level locking before dispatch;
- idempotent processed marker;
- bounded attempts with increasing retry delay;
- failure storage limited to exception class, without payload or credential leakage;
- CLI dispatcher and one-server scheduled execution;
- opt-in production `workers` profile for queue worker and scheduler;
- tests for single processing and fail-closed unknown events.

## Safety boundary

No external publisher or event destination is registered in this stage. Unknown event types remain unprocessed and retry under the configured limit. A concrete handler may only be registered after its destination, payload contract and security requirements are approved.

## Production activation

Run migrations first, then start the opt-in worker profile:

```bash
docker compose -f docker-compose.production.yml --profile workers up -d
```

Worker activation is not a substitute for monitoring failed jobs and pending outbox age.
