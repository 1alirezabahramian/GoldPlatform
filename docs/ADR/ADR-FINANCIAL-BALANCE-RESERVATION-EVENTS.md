# ADR — Financial Balance, Reservation and Event Contracts

## Status
Accepted for domain contract; persistence and business policies remain pending.

## Decisions
- Balance is a projection derived from posted and reserved amounts.
- Available balance is calculated as `posted - reserved` with exact decimal arithmetic.
- Negative financial balances are representable. Permission and credit limits are separate policies and are not decided here.
- Reservations are immutable lifecycle documents with states Active, Released, Captured and Expired.
- Only Active reservations can transition.
- Reservation transitions preserve reservation identity and correlation while requiring a new trace and idempotency key.
- Financial events must carry name, trace ID, correlation ID, idempotency key, occurrence time and payload.
- Custody is excluded because it is a physical asset domain, not a financial balance.

## Explicitly not decided
- Credit limits and customer-group permission rules
- Whether reserving beyond posted balance is allowed for a particular customer
- Database schema and decimal scale
- Projection persistence and rebuild strategy
- Reservation expiry duration
- Event transport, queue and outbox implementation
- Wallet mutation and Kimia write timing
