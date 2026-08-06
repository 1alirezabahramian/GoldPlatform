# GoldPlatform V2 — Kimia Ground Truth

**Stage:** V2-00  
**Status:** IN PROGRESS — evidence register, not a write contract  
**Evidence branch at V2 start:** `recovery/rc2-product-rebuild`  
**Evidence SHA:** `cd92a1144bdfbe043bae1871aab9d623ce8bad64`

## Authority order

1. Sanitized real Kimia API output
2. Official Kimia Swagger/OpenAPI
3. Accepted Project Memory and ADRs
4. Owner-confirmed rules and examples
5. Valid current GitHub code
6. Historical conversations and ZIP files

No Kimia write payload may be enabled from this document alone.

## Final source-of-truth boundary

Kimia is final authority for customer financial assets:

- Money
- Gold
- Coin
- Currency

GoldPlatform is final authority for physical `Custody / Amanat`.

Ledger, Journal, Event Store, Idempotency Registry, Reservation and Balance Projection are internal evidence/workflow mechanisms. They are not final customer balance authorities.

## Confirmed read contracts

| Subject | Contract | Evidence status |
|---|---|---|
| Retail accounts | `GET /api/account` with query `Type=3` | CONFIRMED BY SWAGGER AND PROJECT TEST EVIDENCE |
| Account groups | `GET /api/account/groups` with `accountType` | CONFIRMED BY SWAGGER AND PROJECT TEST EVIDENCE |
| Customer balance | `GET /api/voucher/balance/{id}` | CONFIRMED BY SWAGGER / REAL OUTPUT EVIDENCE RECORDED |
| Transactions | `GET /api/voucher/transactions/{id}` | CONFIRMED BY SWAGGER / REAL OUTPUT EVIDENCE RECORDED |
| Coins | `GET /api/product/coins` | CONFIRMED; DYNAMIC CATALOG |
| Currencies | `GET /api/product/currencies` | CONFIRMED; DYNAMIC CATALOG |
| Health | `/healthz` | SWAGGER EVIDENCE |

Transaction pagination starts from page zero. Boolean query values must be serialized in Kimia-compatible form.

## Runtime-confirmed trade-side mapping

Project Memory records inspection of real transactions for AccountId `350`:

| Customer intent | Kimia business side | Operational/form code | Swagger API Action |
|---|---|---:|---:|
| Customer buys gold | Kimia sells | 4 | 64 |
| Customer sells gold | Kimia buys | 3 | 32 |

Additional confirmed values:

- Money product code: `4`
- Paper-gold fineness in the confirmed mapping: `750`
- Operational/form codes must never be sent as Swagger API Actions.

This resolves the historical `3/4` versus `32/64` wording conflict by separating two code systems. It does **not** authorize a Kimia write implementation.

## Confirmed account and product classifications

### Account types

`1` wholesale, `3` retail, `5` capital/withdrawal, `6` bank, `8` internal, `9` melting, `10` amanat, `11` expense, `12` employees.

### Swagger action vocabulary

`2` receive, `4` pay, `8` return receive, `16` return pay, `32` buy, `64` sell, `128` exchange, `256` clear/pass, `512` collect, `1024` on-account, `2048` returned purchase.

These values are endpoint-specific. A generic Action constant must not be reused blindly across different endpoints.

### Gold units

`0` mithqal, `1` gram, `2` ounce, `3` kilogram.

### Coin types

`15` bank coin, `17` miscellaneous coin.

Coin IDs and Currency IDs are dynamic and must never be treated as stable sample identifiers.

## Decimal and unit requirements

- Money and weight must use exact Decimal or decimal strings, never float.
- Kimia money is Rial; customer platform presentation is Toman.
- Rial/Toman conversion belongs only to one central, explicit, tested Backend boundary.
- Negative financial balances are valid values and must not be replaced by zero.
- Frontend must not calculate Weight750, financial balances, valuation or Rial/Toman conversion.

## Known real-output evidence

Project evidence records:

- `kimia:sync-groups` returned 10 groups.
- `kimia:sync-coins` returned 67 coins.
- `kimia:inspect-transactions --id=350` returned 8 transaction records.
- Runtime transaction evidence established the customer-buy/API-sell and customer-sell/API-buy mapping above.

Raw sensitive outputs and credentials must not be committed. Future evidence must be sanitized while preserving field names, types and relationships.

## Read/write separation

### Read

Read clients may use controlled retry appropriate to idempotent GET operations, explicit timeouts, neutral error translation and privacy-safe logging.

### Write

Kimia Write remains `BLOCKED BY GROUND TRUTH` and deny-by-default until all of these exist:

- exact endpoint and payload from official Swagger;
- sanitized real request/response evidence;
- owner-approved business meaning;
- endpoint-specific action mapping;
- idempotency design and RequestId behavior;
- no automatic retry policy unless explicitly proven safe;
- intent/result audit trail;
- post-success balance readback from Kimia;
- reconciliation and incomplete-operation handling;
- exact executed tests and green CI SHA.

## Unresolved ground truth

| Topic | Status |
|---|---|
| Exact Weight750 formula for every product path | BLOCKED BY GROUND TRUTH |
| Coin/currency write payloads and conversion semantics | BLOCKED BY GROUND TRUTH |
| Physical receive/pay endpoint mapping | BLOCKED BY GROUND TRUTH |
| GoldUnit conversion behavior and SumMoney rules | BLOCKED BY GROUND TRUTH |
| Cent/commission behavior in Kimia | BLOCKED BY GROUND TRUTH |
| Error response schemas and retry safety for writes | BLOCKED BY REAL OUTPUT |
| Authenticated customer-to-Kimia account resolution | REUSE AFTER FIX / NOT CLOSED |

## Mandatory implementation boundary

Controllers must never call a Kimia client directly. Application services must depend on read repositories or a future approved write gateway. Raw Kimia codes, identifiers and accounting terms must remain inside integration and audit boundaries and must not be exposed to customer UI.
