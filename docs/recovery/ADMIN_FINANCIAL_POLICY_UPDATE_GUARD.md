# Admin Financial Policy Update Guard

Status: Implemented — CI Pending

## Decision

The legacy Admin endpoint may continue to list existing `CustomerTradingPolicy` records, but it must not modify financial policy fields until their business rules and Kimia authority boundaries are verified against accepted ground truth.

The update endpoint therefore fails closed with HTTP 503 and code:

`FINANCIAL_POLICY_GROUND_TRUTH_REQUIRED`

## Protected fields

This guard prevents unverified mutation of fields including available-balance requirements, negative-balance allowance, credit limits, financial limits, and order limits.

## Preserved behavior

- Existing policy records are not deleted or rewritten.
- Admin read access remains available.
- Audit logs and outbox reads remain available.
- Operator delivery workflows are unchanged.

## Safety

- No Kimia Write.
- No financial formula or Action Code.
- No migration or data deletion.
- No tenant or permission redesign.
