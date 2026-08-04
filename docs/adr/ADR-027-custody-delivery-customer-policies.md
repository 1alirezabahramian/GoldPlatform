# ADR-027 — Custody, Delivery and Customer Trading Policies

- Status: Accepted
- Date: 2026-08-04

## Decision

1. Physical custody is stored independently from financial Wallet/Ledger accounts.
2. Custody records are retained after delivery, resale or conversion and use terminal statuses instead of deletion.
3. Delivery has its own auditable lifecycle and requires verified receiver information at completion.
4. Customer limits are data-driven per user group through `customer_trading_policies`; no group ID is hard-coded.
5. Money and weight comparisons use exact decimal strings.
6. Resale and conversion of custody require an explicit financial reference. Kimia write payloads remain out of scope until confirmed by real API evidence.

## Consequences

- Custody cannot accidentally inflate or reduce financial balances.
- Double delivery and double conversion are blocked.
- White-label tenants can configure group policies without changing domain code.
- Confirmed Normal/VIP/Super VIP rules are represented as data, not conditionals tied to sample IDs.
