# ADR-023 — Customer Trade Side Mapping at the Kimia Boundary

Status: Accepted

Date: 2026-08-02

## Context

GoldPlatform names an order from the customer's point of view, while Kimia records the
same transaction from the business/accounting point of view. Using the words `buy` and
`sell` without identifying the actor caused contradictory mappings in the project
documentation.

## Decision

The confirmed mapping is:

| GoldPlatform customer operation | Kimia business operation |
|---|---|
| Customer buys | Business sells to customer |
| Customer sells | Business buys from customer |

This mapping was confirmed by the project owner on 2026-08-02.

The canonical domain code contract is `App\Enums\KimiaTradeSide`. It intentionally contains
no numeric API value.

## Transport Encoding Stop Condition

Two different numeric systems are present in the accepted sources:

| Meaning | Owner-confirmed operational/form code | Swagger API Action |
|---|---:|---:|
| Business buys from customer | `3` | `32` |
| Business sells to customer | `4` | `64` |

Swagger defines `32/64` for `ExchangeRequest`, `ExchangeCurrencyRequest`, and `RecordDto`.
The project owner confirmed `3/4` for the operational Kimia workflow. These representations
must not be treated as interchangeable.

No numeric trade Action may be encoded into a live API request until one real paper-gold
buy record and one real paper-gold sell record are read from
`GET /api/voucher/transactions/{id}` and compared with the Swagger contract.

## Consequences

- UI and Order values remain customer-oriented: `buy` and `sell`.
- Kimia payloads remain business/accounting-oriented.
- Unsupported order types fail explicitly instead of silently choosing an Action.
- Kimia Action codes must never be shown to the customer.
- The semantic trade side can be implemented without prematurely choosing an API number.

## Scope Boundary

This ADR confirms only the trade direction mapping. It does not yet define the complete
voucher payload, idempotency key, retry policy, product/conversion codes, or the moment at
which an approved order is posted to Kimia. Those items require separate verified API
evidence before enabling live financial writes.
