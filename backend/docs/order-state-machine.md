# Order State Machine

## Statuses

- `pending`
- `approved`
- `executing`
- `settling`
- `completed`
- `rejected`
- `expired`
- `cancelled`
- `failed`

## Allowed transitions

```text
pending   -> approved | rejected | cancelled | expired
approved  -> executing | cancelled | expired
executing -> settling | failed
settling  -> completed | failed
```

`completed`, `rejected`, `expired`, `cancelled`, and `failed` are terminal states.

## Invariants

- Every transition runs inside a database transaction.
- The order row is locked with `lockForUpdate` before a transition.
- Repeating the same transition is idempotent and does not increment `state_version`.
- Rejection and failure require a non-empty reason.
- Expiration is allowed only after `expires_at`.
- Each successful state change increments `state_version` and records its timestamp.
- Financial calculations and Kimia writes are outside the state machine.
