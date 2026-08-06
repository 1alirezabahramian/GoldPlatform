# Operator Delivery Action Response Redaction

Status: Implemented — CI Pending

## Scope

The existing operator delivery action endpoints now return explicit allowlisted response fields instead of serializing the complete `DeliveryRequest` model.

Covered endpoints:

- `POST /api/operator/deliveries/{deliveryRequest}/approve`
- `POST /api/operator/deliveries/{deliveryRequest}/ready`
- `POST /api/operator/deliveries/{deliveryRequest}/deliver`

## Redacted fields

The HTTP action responses do not expose:

- `receiver_name`
- `receiver_identifier`
- `metadata`
- internal status reason fields

Audit and outbox recording remain unchanged.

## Duplicate review

PR #135 contained an earlier versioned delivery-action controller with a safe response pattern. It was closed without merge and was based on an old stacked Admin/Operator branch. Its response pattern was reviewed and reused in the current canonical controller without reviving duplicate routes or controllers.

## Safety boundaries

- No financial rule changes.
- No Kimia read or write changes.
- No balance changes.
- No migration or route changes.
- No permission or tenant architecture changes.
- Branch/company scoping remains blocked by architecture ground truth.

## Validation

A feature test executes the complete transition sequence `requested -> approved -> ready -> delivered` and verifies that receiver identity and metadata are absent from all three HTTP responses.
