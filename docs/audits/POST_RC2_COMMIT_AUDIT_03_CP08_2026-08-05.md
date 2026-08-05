# Post-RC2 Commit Audit 03 — CP-08

## Status

DONOR — REBUILD REQUIRED

## Pull Request

- PR: #102
- Title: CP-08: add customer activity timeline contract
- Base SHA: `22a4c7bb21744fb585de272807bb3c43a7184c17`
- Head SHA: `fd04368cc51ec0dae172223826599d27bb0d8305`
- Merge Commit: `247a4147649912c2c47361df5cef481c6a765014`
- Merged: yes

## CI Evidence

All six RC2 gates passed on the exact Head SHA:

- Backend RC1 Validation — PASS
- Backend RC2 Candidate — PASS
- Security Hardening — PASS
- Stage 21 Performance — PASS
- Production Compose Validation — PASS
- Backup and Restore Drill — PASS

## Scope

The PR adds:

- `GET /api/v1/customer/activities`
- `CustomerActivityController`
- `CustomerActivityReadModel`
- architecture contract test
- CP-08 documentation

It does not add a migration, Kimia write, financial rule, Wallet/Ledger/Settlement mutation, or a new domain event.

## Verified strengths

- Endpoint is authenticated and customer-scoped.
- All three source queries are constrained by authenticated `user_id`.
- `per_page` is bounded to 50.
- `event_type` is allow-listed.
- Responses use `CustomerReadPresenter` rather than raw model serialization.
- No internal IDs or metadata are intentionally exposed.

## Semantic contract drift

The PR and its documentation describe the endpoint as a timeline of customer activity and status changes.

The implementation does not read an event store, transition history, audit log, order status history, custody history, or delivery history. It reads one current row for each Order, CustodyAsset, and DeliveryRequest, maps the row's current status, and uses the row's current `updated_at` value as `occurred_at`.

Therefore:

- it is a current-resource activity feed;
- it is not a historical timeline of status transitions;
- multiple transitions of the same resource cannot appear;
- a later unrelated update can move an item in the timeline;
- the wording "changes" and "timeline" overstates what the data actually proves.

This is the first confirmed post-RC2 semantic drift found in the chronological audit. CI remained green because the tests are architecture/source-code guards and do not verify historical event semantics.

## Recovery decision

Do not forward-port CP-08 as-is.

Preserve these donor parts:

- authenticated route shape;
- ownership constraints;
- bounded pagination and filter allow-list;
- safe presenter usage;
- response envelope.

Rebuild one of these explicit contracts before integration:

1. **Current activity snapshot/feed** — rename the endpoint/fields/documentation so it truthfully represents current resources ordered by `updated_at`; or
2. **True activity timeline** — source records from accepted audit/event/transition history and add integration tests proving multiple historical transitions per resource.

No choice between these two is made in Recovery because that affects customer experience and the canonical API contract.

## Classification

`CP-08 = DONOR — REBUILD REQUIRED`

## Boundary

- RC2 remains the canonical baseline.
- CP-06 and CP-07 remain verified donors.
- CP-08 is the first confirmed semantic contract drift.
- Later commits must still be audited independently; they are not automatically rejected solely because they descend from CP-08.
