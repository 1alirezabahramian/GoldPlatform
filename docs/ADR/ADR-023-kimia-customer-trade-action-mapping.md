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
no numeric API value. The separate transport contract is
`App\Enums\KimiaApiTradeAction`.

## Confirmed Transport Encoding

Two different numeric systems are present in the accepted sources:

| Meaning | Owner-confirmed operational/form code | Swagger API Action |
|---|---:|---:|
| Business buys from customer | `3` | `32` |
| Business sells to customer | `4` | `64` |

Swagger defines `32/64` for `ExchangeRequest`, `ExchangeCurrencyRequest`, and `RecordDto`.
The project owner confirmed `3/4` for the operational Kimia workflow. These representations
are separate contracts and must not be treated as interchangeable.

The stop condition was resolved on 2026-08-02 by a read-only response from
`GET /api/voucher/transactions/350`:

| RecordId | API Action | ActionName | ProductId | ProductName | Evidence |
|---:|---:|---|---:|---|---|
| `75796` | `32` | خرید | `4` | پولی | Kimia buys from the customer |
| `74007` | `64` | فروش | `4` | پولی | Kimia sells to the customer |

Therefore the API trade encoding is final:

| Customer order | Kimia business side | `KimiaApiTradeAction` | API value |
|---|---|---|---:|
| `buy` | Sell to customer | `SellToCustomer` | `64` |
| `sell` | Buy from customer | `BuyFromCustomer` | `32` |

Operational/form codes `3/4` remain valid only in their own workflow context and are not
valid `KimiaApiTradeAction` values.

## Consequences

- UI and Order values remain customer-oriented: `buy` and `sell`.
- Kimia payloads remain business/accounting-oriented.
- Unsupported order types fail explicitly instead of silently choosing an Action.
- Unit tests lock customer `buy → 64` and customer `sell → 32` and reject `3/4` as API
  trade values.
- Kimia Action codes must never be shown to the customer.
- Confirming the numeric mapping does not enable any live Kimia write.

## Scope Boundary

This ADR confirms only the trade direction and numeric API trade Action mapping. It does
not yet define the complete voucher payload, idempotency key, retry policy,
product/conversion codes, or the moment at which an approved order is posted to Kimia.
Those items require separate verified API evidence before enabling live financial writes.
