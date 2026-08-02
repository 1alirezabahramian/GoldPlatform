# GoldPlatform — Kimia API Integration Audit

**Document type:** Technical audit and integration reference  
**Project:** GoldPlatform  
**Module:** Kimia Accounting API Integration  
**Status:** Living audit; read paths under controlled stabilization

**Last updated:** 2026-08-02

---

## 1. Purpose

This document records the current verified understanding of the Kimia API and how it should be integrated into GoldPlatform.

The document is based on these sources:

1. Real Kimia API responses
2. Kimia Swagger documentation
3. Owner-confirmed operational evidence
4. Accepted project memory and ADRs
5. Current GoldPlatform source structure

This document is an audit and design reference. It does not mean that the current implementation is already complete or correct.

---

## 2. Source-of-truth policy

When sources disagree, use this priority:

1. **Real Kimia API response**
2. **Official Swagger JSON**
3. **Accepted project memory and ADRs**
4. **Owner-confirmed operational evidence**
5. **Current GoldPlatform code**
6. **Older documentation**

Swagger defines the official transport contract; real responses confirm server behavior.
Operational/form codes and API Action values are separate contracts and must not be merged.

---

## 3. Confirmed API modules

The reviewed Swagger exposes the following modules:

- Account
- Barcode
- Home
- Product
- Report
- RFID
- Voucher
- Wallet

---

## 4. Confirmed endpoint map

### 4.1 Account

| Method | Endpoint | Purpose | Status |
|---|---|---|---|
| GET | `/api/account` | Account list and filtered account lookup | Verified |
| POST | `/api/account` | Create account | Verified |
| PUT | `/api/account` | Update account | Verified |
| GET | `/api/account/groups` | Account groups list | Verified |

> `GET /api/account/{id}` has not been confirmed in the reviewed Swagger section and was not observed in the supplied runtime logs. Any implementation using this route must be treated as suspicious until independently verified.

---

### 4.2 Barcode

| Method | Endpoint | Purpose | Status |
|---|---|---|---|
| GET | `/api/barcode` | Barcode list/search | Visible in Swagger |
| GET | `/api/barcode/exists/{id}` | Barcode existence check | Visible in Swagger |

The exact parameters and response schema still require detailed extraction.

---

### 4.3 Product catalog

| Method | Endpoint | Purpose | Status |
|---|---|---|---|
| GET | `/api/product` | Product list | Verified |
| GET | `/api/product/coins` | Coin list | Verified |
| GET | `/api/product/currencies` | Currency list | Verified |

These endpoints accept no parameters.

---

### 4.4 Report

| Method | Endpoint | Purpose | Status |
|---|---|---|---|
| GET | `/api/report/dealsbalance` | Deals balance report | Verified by Swagger and runtime log |

Observed query example:

```text
?range=0&productId=-2
```

Observed response example:

```text
9.878000000000000
```

---

### 4.5 RFID

| Method | Endpoint | Purpose | Status |
|---|---|---|---|
| GET | `/api/rfid` | RFID list/search | Visible in Swagger |
| POST | `/api/rfid/inventory` | RFID inventory operation | Visible in Swagger |

Request and response contracts still require detailed extraction.

---

### 4.6 Voucher

| Method | Endpoint | Purpose | Status |
|---|---|---|---|
| GET | `/api/voucher/balance/{id}` | Account balance | Verified |
| GET | `/api/voucher/balances` | Multiple account balances | Visible in Swagger |
| GET | `/api/voucher/transactions/{id}` | Account transactions | Verified |
| POST | `/api/voucher/adjustment` | Add or subtract balance | Visible in Swagger |
| POST | `/api/voucher/exchangecurrency` | Currency exchange | Visible in Swagger |
| POST | `/api/voucher/exchangegold` | Gold exchange | Visible in Swagger |
| POST | `/api/voucher/tradebarcode` | Barcode trade | Visible in Swagger |
| POST | `/api/voucher/tradecash` | Cash trade | Visible in Swagger |
| POST | `/api/voucher/tradecurrency` | Currency trade | Visible in Swagger |
| POST | `/api/voucher/transfergold` | Gold transfer | Visible in Swagger |
| POST | `/api/voucher/transfermoney` | Money transfer | Verified by runtime log |
| DELETE | `/api/voucher/deleterecord` | Delete voucher record | Visible in Swagger |

Observed transaction query examples:

```text
?descending=true&pageNumber=0&pageSize=20
?descending=true&pageNumber=1&pageSize=20
```

Observed `transfermoney` request body:

```json
{
  "AccountId": 409,
  "AddToExistingDateVoucher": false,
  "Date": null,
  "Comment": "شارژ حساب از سایت",
  "Action": 2,
  "TargetId": 5,
  "Value": 700000000,
  "CurrencyId": null
}
```

Observed response:

```text
75030
```

This likely represents a created record or voucher identifier, but that meaning must be confirmed from Swagger.

---

### 4.7 Wallet

| Method | Endpoint | Purpose | Status |
|---|---|---|---|
| GET | `/api/wallet/list` | Wallet list | Visible in Swagger |
| GET | `/api/wallet/list/{id}` | Wallet list/details by identifier | Visible in Swagger |
| GET | `/api/wallet/payments` | Wallet payments list | Visible in Swagger |
| GET | `/api/wallet/payments/{id}` | Wallet payment details/list by identifier | Visible in Swagger |
| POST | `/api/wallet` | Create wallet | Visible in Swagger |
| DELETE | `/api/wallet` | Delete wallet | Visible in Swagger |
| POST | `/api/wallet/transfer` | Wallet transfer | Visible in Swagger |

Request and response details still require full schema extraction.

---

### 4.8 Health endpoints

| Method | Endpoint | Purpose | Status |
|---|---|---|---|
| GET | `/healthz` | Service health check | Visible in Swagger |
| HEAD | `/healthz` | Service health check | Visible in Swagger |

---

## 5. Runtime log observations

The supplied daily runtime log shows a repeated request pattern approximately every ten minutes:

```text
GET /api/account
GET /api/product
GET /api/product/coins
GET /api/product/currencies
```

This strongly suggests that these four endpoints are used as periodically refreshed master data.

This is an inference from the log pattern, not an explicit Swagger statement.

Other confirmed runtime usage includes:

- Repeated balance checks using `/api/voucher/balance/{id}`
- Paginated transaction retrieval using `/api/voucher/transactions/{id}`
- Money transfer using `/api/voucher/transfermoney`
- Deals balance reporting using `/api/report/dealsbalance`
- Unauthorized access to `/logs` returning HTTP 401

Observed unauthorized response:

```text
هویت شما شناسایی نشد
```

---

## 6. Confirmed schemas

## 6.1 AccountDto

Represents a Kimia account.

| Field | Type | Nullable | Notes |
|---|---|---:|---|
| AccountId | integer | Yes | Account identifier |
| AccountCode | Not fully extracted | — | Requires exact schema extraction |
| Name | string, max 255 | Yes | Full name |
| NationalCode | string, max 20 | Yes | National code |
| ShopName | string, max 255 | Yes | Shop name |
| EconomicCode | string, max 20 | Yes | Economic code |
| Tel | string, max 255 | Yes | Telephone |
| Mobile | string, max 255 | Yes | Mobile |
| PostalCode | string, max 10 | Yes | Postal code |
| Address | string, max 255 | Yes | Address |
| DateBirthday | date-time | Yes | Date of birth |
| Comment | string, max 500 | Yes | Notes |
| Type | integer | Yes | Account type |
| IsVisible | boolean | Yes | Default true |

Account type values:

| Value | Meaning |
|---:|---|
| 1 | بنکداری |
| 3 | تکفروشی |
| 5 | سرمایه و برداشت |
| 6 | بانک |
| 8 | حساب داخلی |
| 9 | ذوب |
| 10 | امانات |
| 11 | هزینه |
| 12 | کارمندان |

Important Swagger constraint:

```json
{
  "additionalProperties": false
}
```

Do not send undocumented extra properties in `AccountDto`.

---

## 6.2 AccountGroupDto

Represents an account group.

| Field | Type | Nullable | Notes |
|---|---|---:|---|
| Id | integer | No explicit nullable flag | Group identifier |
| Name | string, max 255 | Yes | Group name |
| AccountType | integer | Yes | Account type |

`AccountType` uses the same numeric values as `AccountDto.Type`.

Important distinction:

- `/api/account` filtering uses `Type`
- `/api/account/groups` uses `accountType` as the related account type parameter

A current repository method that sends `accountType` to `/api/account` is inconsistent with the reviewed Swagger and should be corrected after final verification.

---

## 6.3 ProductDto

Represents a Kimia product record.

| Field | Type | Nullable | Notes |
|---|---|---:|---|
| ProductId | integer | Yes | Product identifier |
| Name | string, max 255 | Yes | Product name |
| Fineness | decimal | Yes | Fineness |
| Weight | decimal | Yes | Weight |
| Type | integer | Read-only | Product type |
| IsVisible | boolean | Yes | Visibility |

Product type values:

| Value | Meaning |
|---:|---|
| 11 | ساخته |
| 12 | کالا |
| 13 | نیم‌ساخته |

`Type` is read-only and should not be treated as a writable field unless future Swagger evidence says otherwise.

---

## 6.4 Product endpoint responses

### `GET /api/product`

Returns an array of `ProductDto`.

Example:

```json
[
  {
    "ProductId": 0,
    "Name": null,
    "Fineness": 0,
    "Weight": 0,
    "Type": 0,
    "IsVisible": true
  }
]
```

### `GET /api/product/coins`

Returns an array of `CoinDto`.

Example:

```json
[
  {
    "CoinId": 0,
    "Name": null,
    "Fineness": 0,
    "Weight": 0,
    "Type": 0,
    "IsVisible": true
  }
]
```

### `GET /api/product/currencies`

Returns an array of `CurrencyDto`.

Example:

```json
[
  {
    "CurrencyId": 0,
    "Name": null,
    "IsVisible": true
  }
]
```

Products, coins, and currencies are separate API resources with separate DTOs. They must not be modeled as one shared entity merely because they are under the `Product` Swagger section.

---

## 6.5 AdjustmentRequest

Represents an add/subtract accounting request.

| Field | Type | Required | Nullable | Notes |
|---|---|---:|---:|---|
| RequestId | string, max 36 | No | Yes | Unique request identifier |
| AddToExistingDateVoucher | boolean | No | No | Default false |
| AccountId | integer | Yes | No | Account identifier |
| Date | date-time | No | Yes | Date |
| Comment | string, max 500 | No | Yes | Notes |
| Action | integer | Yes | No | Add/subtract action |
| TagId | integer | No | Yes | Tag identifier |
| Money | decimal | No | Yes | Amount |
| CurrencyId | integer | No | Yes | Currency identifier |
| Weight | decimal | No | Yes | Weight in grams |
| Fineness | decimal | No | Yes | Weight fineness |

Action values:

| Value | Meaning |
|---:|---|
| 2 | اضافه |
| 4 | کسر |

`RequestId` is intended to prevent duplicate voucher registration. UUID version 4 is recommended.

GoldPlatform must generate and persist an idempotency identifier for mutation requests where supported.

---

## 6.6 BalanceDto

Represents an account balance.

| Field | Type | Nullable | Notes |
|---|---|---:|---|
| AccountId | integer | No explicit nullable flag | Account identifier |
| AccountName | string, max 255 | Yes | Account name |
| GroupId | integer | No explicit nullable flag | Group identifier |
| Weight | decimal | Yes | Gold weight |
| Money | decimal | Yes | Monetary balance |
| CurrencyId | integer | No explicit nullable flag | Currency identifier |
| CurrencySymbol | string, max 255 | Yes | Read-only currency symbol |
| GoldPeaks | PeakDto[] | Yes | Gold peaks |
| MoneyPeaks | PeakDto[] | Yes | Money peaks |

Balance values may be positive or negative. Their business meaning must not be assumed without explicit Kimia documentation and project business rules.

---

## 6.7 PeakDto

Embedded in `BalanceDto`.

| Field | Type | Nullable | Notes |
|---|---|---:|---|
| Date | date-time | Yes | Peak date |
| Days | integer | Yes | Number of days |
| Value | decimal | Not specified as nullable | Gold weight or monetary value |

---

## 7. Current GoldPlatform code audit

The active read/synchronization flow confirmed on `audit/kimia-foundation` is:

```text
App\Services\KimiaService
    ↓
App\Repositories\Kimia\AccountRepository
App\Repositories\Kimia\VoucherRepository
    ↓
Console sync/inspection commands
```

Current verified endpoints and query contracts:

```text
GET /api/account                       → Type
GET /api/account/groups                → accountType
GET /api/voucher/transactions/{id}     → pageNumber/pageSize/descending
GET /api/voucher/balance/{id}          → includePeaks (optional)
```

Current concerns:

1. `GET /api/account/{id}` is unverified.
2. `AccountRepository::all()` and `groups()` still use tolerant empty results on HTTP failure;
   this conflicts with the accepted error policy and requires a separate tested correction.
3. The self-contained `App\Integrations\Kimia` account tree has no external references in
   the current repository, but deletion remains a separate controlled cleanup.
4. Live voucher writes remain disabled.

Legacy paths already removed during stabilization:

```text
App\Services\Kimia\KimiaService
App\Clients\KimiaClient
App\Services\Kimia\KimiaClient
App\Services\Kimia\AccountRepository
App\Services\Kimia\AccountService
App\Services\Kimia\CustomerService
App\Services\Kimia\BaseKimiaService
```

---

## 8. Domain separation recommendation

GoldPlatform should not use one large `KimiaService` for all operations.

Recommended integration domains:

```text
Kimia/
├── Accounts/
├── Catalog/
│   ├── Products/
│   ├── Coins/
│   └── Currencies/
├── Vouchers/
├── Wallets/
├── Trading/
├── Barcodes/
├── RFID/
└── Reports/
```

Recommended responsibility split:

```text
KimiaClient
    HTTP authentication, headers, timeout, request execution

DTOs
    Typed request and response data

Repositories
    Endpoint-specific remote data access

Mappers
    Kimia DTO ↔ GoldPlatform domain conversion

Services
    GoldPlatform business workflows

Enums
    Account types, product types, voucher actions, and similar constants
```

---

## 9. Naming policy

Kimia data models must not be confused with GoldPlatform domain models.

Recommended names:

```text
KimiaAccountDto
KimiaAccountGroupDto
KimiaProductDto
KimiaCoinDto
KimiaCurrencyDto
KimiaBalanceDto
```

Do not map Kimia `ProductDto` directly to the main GoldPlatform `Product` model.

Kimia Product and GoldPlatform Product are different concepts and may evolve independently.

---

## 10. Error-handling policy

Remote API failures must not silently become empty lists or null unless the caller explicitly requests tolerant behavior.

Recommended categories:

```text
KimiaAuthenticationException
KimiaValidationException
KimiaNotFoundException
KimiaConflictException
KimiaRateLimitException
KimiaServerException
KimiaConnectionException
KimiaUnexpectedResponseException
```

Every mutating operation should log:

- endpoint
- method
- safe request identifier
- HTTP status
- Kimia response
- duration
- GoldPlatform user or process identifier

Secrets and credentials must never be logged.

---

## 11. Idempotency policy

For schemas supporting `RequestId`, GoldPlatform should:

1. Generate UUID v4
2. Save it before sending the request
3. Reuse it on safe retries
4. Never generate a new ID for the same business operation retry
5. Store the Kimia response identifier
6. Prevent duplicate local confirmation

This is especially important for:

- adjustments
- transfers
- wallet payments
- trades
- exchange operations

---

## 12. Master-data synchronization

The log pattern suggests these endpoints behave as master data:

```text
/api/account
/api/product
/api/product/coins
/api/product/currencies
```

Recommended GoldPlatform approach:

- Cache remote master data
- Store last successful sync time
- Keep remote identifiers
- Avoid calling full lists on every customer request
- Use a scheduled sync job
- Preserve the last valid snapshot if Kimia is temporarily unavailable
- Mark records stale instead of silently deleting them

The exact synchronization interval is a project decision. The observed external system uses an approximately ten-minute pattern.

---

## 13. Security requirements

Kimia credentials must exist only in environment variables.

Expected configuration keys:

```env
KIMIA_BASE_URL=
KIMIA_USERNAME=
KIMIA_PASSWORD=
KIMIA_TIMEOUT=30
```

Additional security rules:

- Never expose Kimia credentials to frontend code
- Never return raw Kimia exceptions to customers
- Redact authorization headers and tokens from logs
- Restrict Kimia debug routes to local/admin environments
- Protect any internal log viewer with authentication and authorization
- Validate all outgoing DTOs before transmission

---

## 14. Implementation status matrix

| Area | Swagger reviewed | Runtime observed | GoldPlatform reviewed | Status |
|---|---:|---:|---:|---|
| Account list | Yes | Yes | Yes | Query fixed and mock-tested; live revalidation pending |
| Account create | Yes | No | Yes | Contract review pending |
| Account update | Yes | No | Yes | Contract review pending |
| Account groups | Yes | Historical live sync | Yes | Query fixed and mock-tested; live revalidation pending |
| Product list | Yes | Yes | Partial | Catalog mapping pending |
| Coin list | Yes | Yes | Yes | Sync command mock-tested |
| Currency list | Yes | Yes | Yes | Sync command mock-tested |
| Balance | Yes | Yes | Yes | Read path and mock tests prepared; runtime test pending |
| Transactions | Yes | Yes | Yes | Read path verified on account `350` |
| Money transfer | Endpoint visible | Yes | Not fully | Request schema must be reconciled |
| Adjustment | Schema known | No | Not fully | Ready for design |
| Wallet | Endpoint map visible | No | Not fully | Detailed audit pending |
| Barcode | Endpoint map visible | No | Not fully | Detailed audit pending |
| RFID | Endpoint map visible | No | Not fully | Detailed audit pending |
| Reports | Partial | Yes | Not fully | Detailed audit pending |

---

## 15. Pending schema extraction

The Swagger schemas were exported into:

```text
kimia-schemas-full.json
kimia-schemas-full.md
```

Extraction is complete for the schemas present in the reviewed Swagger snapshot. Business
semantics, required runtime behavior, response edge cases, and mutation safety remain
separate verification work; schema extraction alone does not authorize implementation.

---

## 16. Required next audit steps

1. Run the prepared balance mock tests in the Laravel container.
2. Perform one controlled, read-only balance inspection for account `350`.
3. Revalidate Account and AccountGroup synchronization after stabilization.
4. Correct tolerant AccountRepository error handling with tests.
5. Remove the unused `App\Integrations\Kimia` account tree only in a separate commit after
   test verification.
6. Build DTO/Mapper boundaries from verified read responses.
7. Keep all write endpoints disabled until payload, idempotency, retry, failure, and posting
   time are independently accepted.

---

## 17. Decisions recorded

### Decision 1
Real Kimia API output is the primary source of truth; Swagger JSON defines the official
transport contract beneath that evidence.

### Decision 2
No duplicate Kimia class will be deleted before usage verification.

### Decision 3
Kimia DTOs will be kept separate from GoldPlatform domain models.

### Decision 4
The integration will be divided by domain instead of one large service.

### Decision 5
Mutating operations must support safe retry and idempotency where the API allows it.

### Decision 6
Runtime behavior will be compared with Swagger before changing production-facing code.

---

## 18. Known uncertainties

The following points are not yet confirmed:

- Exact schema of `AccountCode`
- Whether `/api/account/{id}` exists outside the reviewed Swagger path
- Exact meaning of numeric response `75030` from `transfermoney`
- Full query parameters for Barcode and RFID endpoints
- Full request/response contracts for Wallet operations
- Exact sign convention for positive and negative balance values
- Whether all mutation endpoints support `RequestId`
- Whether the ten-minute request pattern belongs to Kimia itself or another connected application

These items must remain marked as unknown until verified.

---

## 19. Final architecture target

```text
GoldPlatform Domain
        ↓
Application Services
        ↓
Kimia Repositories
        ↓
Kimia DTOs and Mappers
        ↓
Kimia HTTP Client
        ↓
Kimia API
```

Kimia-specific field names, numeric codes, authentication behavior, and remote exceptions must stop at the integration boundary and must not leak through the entire GoldPlatform codebase.

---
## CoinDto

Represents a coin in Kimia.

| Field | Type | Nullable | Notes |
|---|---|---:|---|
| CoinId | integer | Yes | Coin identifier |
| Name | string, max 255 | Yes | Coin name |
| Fineness | decimal | Yes | Coin fineness |
| Weight | decimal | Yes | Coin weight |
| Type | integer | Read-only | Coin type |
| IsVisible | boolean | Yes | Visibility status |

Coin type values:

| Value | Meaning |
|---:|---|
| 15 | سکه بانکی |
| 17 | سکه متفرقه | 



## CurrencyDto

Represents a currency in Kimia.

| Field | Type | Nullable | Notes |
|---|---|---:|---|
| CurrencyId | integer | Yes | Currency identifier |
| Name | string, max 255 | Yes | Currency name |
| IsVisible | boolean | Yes | Visibility |

### Notes

- Currency is an independent entity.
- It has no Weight.
- It has no Fineness.
- It has no Type.
- Currency identifiers are referenced by multiple request and response DTOs including Balance, Adjustment, Transfer, Exchange and Wallet operations.


## VoucherDto

Represents the identifiers generated for a Kimia voucher operation.

| Field | Type | Nullable | Notes |
|---|---|---:|---|
| Voucher | integer | Not specified | Voucher identifier |
| Records | integer[] | Yes | Identifiers of the generated voucher records |

### Notes

- The API property is named `Voucher`, not `VoucherId`.
- `Records` contains record identifiers, not full `RecordDto` objects.
- The relationship between this DTO and the numeric response observed from `/api/voucher/transfermoney` is not yet confirmed.
- Raw Kimia field names should be preserved at the transport boundary and mapped to clearer domain names such as `voucherId` and `recordIds`.

## RecordDto

Represents an accounting voucher record in Kimia.

### Identity and account fields

| Field | Type | Nullable | Notes |
|---|---|---:|---|
| AccountId | integer | Not specified | Account identifier |
| AccountCode | integer | Not specified | Account code |
| AccountName | string, max 255 | Yes | Account name |
| VoucherId | integer | Not specified | Voucher identifier |
| VoucherNumber | integer | Not specified | Voucher number |
| RecordId | integer | Not specified | Record identifier |
| Date | date-time | Yes | Record date |

### Action fields

| Field | Type | Nullable | Notes |
|---|---|---:|---|
| Action | integer | Yes | Accounting action |
| ActionName | string, max 255 | Yes, read-only | Action title |

Action values:

| Value | Meaning |
|---:|---|
| 2 | دریافت |
| 4 | پرداخت |
| 8 | دریافت مرجوعی |
| 16 | پرداخت مرجوعی |
| 32 | خرید |
| 64 | فروش |
| 128 | تعویض |
| 256 | پاس شدن |
| 512 | وصول |
| 1024 | در حساب |
| 2048 | خرید مرجوعی |

### Product and gold fields

| Field | Type | Nullable | Notes |
|---|---|---:|---|
| ProductId | integer | Yes | Product identifier |
| ProductName | string, max 255 | Yes | Product name |
| Description | string, max 255 | Yes | Description |
| Weight | decimal | Yes | Weight in grams |
| Fineness | decimal | Yes | Gold fineness |
| UnitPrice | decimal | Yes | Making charge unit price |
| Cent | decimal | Yes | Making charge percentage |
| GoldUnit | integer | Yes | Gold unit |
| GoldUnitName | string, max 255 | Yes, read-only | Gold unit title |
| GoldPrice | decimal | Yes | Gold price |
| Quantity | decimal | Yes | Quantity |
| Weight750 | decimal | Not specified | Equivalent 750 fineness weight |

Gold unit values:

| Value | Meaning |
|---:|---|
| 0 | مثقال |
| 1 | گرم |
| 2 | اونس |
| 3 | کیلوگرم |

### Monetary and cumulative fields

| Field | Type | Nullable | Notes |
|---|---|---:|---|
| SumMoney | decimal | Not specified | Monetary amount |
| CurrencyId | integer | Not specified | Currency identifier |
| CurrencySymbol | string, max 255 | Yes | Currency symbol |
| Comment | string, max 500 | Yes | Comment |
| CumulativeWeight750 | decimal | Yes | Cumulative 750 weight |
| CumulativeSumMoney | decimal | Yes | Cumulative monetary amount |
| RelatedRecord | RecordDto | Not confirmed | Related accounting record |

### Notes

- `RelatedRecord` is a recursive reference to another `RecordDto`.
- Swagger displays the recursive schema repeatedly, but the actual response should terminate with a null or absent related record.
- The business meaning of positive and negative `SumMoney` and `Weight750` values still requires verification.
- `ActionName` and `GoldUnitName` are read-only display fields.


## Voucher Module Architecture

The Voucher module is the accounting core of the Kimia API.

Verified endpoints:

- GET /api/voucher/balance/{id}
- GET /api/voucher/balances
- GET /api/voucher/transactions/{id}
- POST /api/voucher/adjustment
- POST /api/voucher/exchangecurrency
- POST /api/voucher/exchangegold
- POST /api/voucher/tradecash
- POST /api/voucher/tradecurrency
- POST /api/voucher/tradebarcode
- POST /api/voucher/transfermoney
- POST /api/voucher/transfergold
- DELETE /api/voucher/deleterecord

Kimia pagination starts from page zero.

GoldPlatform public APIs should continue using page one.

Conversion must happen inside the integration layer.


### ADR-007

Kimia pagination is not exposed to the application.

The integration layer converts:

Application

page=1

↓

Kimia

PageNumber=0

### ADR-008

Raw Kimia DTOs must never be returned directly
to Controllers.

Controllers only receive mapped Domain DTOs.

RelatedRecord is recursive.

Implementation must support nullable recursion.

Infinite recursion must be prevented during mapping.


Create dedicated enums for:

KimiaRecordAction

KimiaGoldUnit


Represents a transfer request used by both:

POST /api/voucher/transfermoney

POST /api/voucher/transfergold

### Required fields

- AccountId
- Action
- TargetId
- Value

### Optional fields

- RequestId
- Date
- Comment
- CurrencyId
- AddToExistingDateVoucher

### Action values

2 = Receive

4 = Pay

### Notes

- RequestId is intended for idempotency.
- UUID version 4 is recommended by the API documentation.
- The semantic meaning of Value depends on the endpoint:
  - Monetary amount for transfermoney.
  - Gold weight for transfergold.
- CurrencyId is expected to be used for money transfers. Its behavior for gold transfers must be confirmed.

## TradeCashRequest

Represents a cash trade request.

Endpoint:

POST /api/voucher/tradecash

| Field | Type | Required | Nullable | Notes |
|---|---|---:|---:|---|
| RequestId | string, max 36 | No | Yes | Unique idempotency identifier |
| AddToExistingDateVoucher | boolean | No | No | Add to a voucher with a matching date |
| AccountId | integer | Yes | No | Account identifier |
| Date | date-time | No | Yes | Voucher date |
| Comment | string, max 500 | No | Yes | Comment |
| Action | integer | Yes | No | Receive or pay |
| Value | decimal | Yes | No | Trade value |

Action values:

| Value | Meaning |
|---:|---|
| 2 | دریافت |
| 4 | پرداخت |

### Notes

- UUID version 4 is recommended for RequestId.
- RequestId must be reused when retrying the same business operation.
- Unlike TransferRequest, this request has no TargetId.
- Unlike TransferRequest, this request has no CurrencyId.
- Swagger does not explicitly define the monetary unit of Value.

## TradeCurrencyRequest

Represents a currency or coin trade request.

Endpoint:

POST /api/voucher/tradecurrency

| Field | Type | Required | Nullable | Notes |
|---|---|---:|---:|---|
| RequestId | string, max 36 | No | Yes | Idempotency identifier |
| AddToExistingDateVoucher | boolean | No | No | Add to an existing voucher on the same date |
| AccountId | integer | Yes | No | Account identifier |
| Date | date-time | No | Yes | Voucher date |
| Comment | string, max 500 | No | Yes | Comment |
| Action | integer | Yes | No | Receive or pay |
| TargetId | integer | Yes | No | Currency or coin identifier |
| Value | decimal | Yes | No | Trade quantity |

### Action values

| Value | Meaning |
|---:|---|
| 2 | دریافت |
| 4 | پرداخت |

### Notes

- `TargetId` represents a currency or coin identifier, not an account.
- `RequestId` should be a UUID version 4 and reused for retries.
- The Swagger specification does not define the unit represented by `Value`; it should be interpreted according to the selected target resource.


## TradeBarcodeRequest

Represents a transaction involving one or more barcode-identified physical products.

Endpoint:

POST /api/voucher/tradebarcode

### Required fields

- AccountId
- Action
- BarcodeIds
- GoldPrice

### Optional fields

- RequestId
- Date
- Comment
- GoldUnit
- AddToExistingDateVoucher

### Action values

| Value | Meaning |
|------:|---------|
|2|Receive|
|4|Pay|
|8|Receive Return|
|16|Pay Return|
|32|Buy|
|64|Sell|
|128|Exchange|
|2048|Buy Return|

### Notes

- Transactions operate on physical products identified by barcode.
- Multiple barcode identifiers can be submitted in a single request.
- `GoldPrice` is mandatory.
- `GoldUnit` is optional.
- The Swagger specification does not define custody or delivery behavior; those business rules must be confirmed from runtime behavior or additional documentation.

## ExchangeRequest

Represents a gold exchange ("monetization") request.

Endpoint:

POST /api/voucher/exchangegold

### Required fields

- AccountId
- Action
- GoldPrice
- Value

### Optional fields

- RequestId
- Date
- Comment
- CurrencyId
- GoldUnit
- AddToExistingDateVoucher

### Action values

| Value | Meaning |
|------:|---------|
|32|Buy|
|64|Sell|

### Notes

- `GoldPrice` is mandatory.
- `GoldUnit` is optional.
- `CurrencyId` is optional.
- `RequestId` should use UUID version 4.
- The Swagger specification describes `Value` only as "the amount to be monetized" and does not explicitly define whether it represents weight or monetary value.



## 20. Read-only balance checkpoint — 2026-08-02

The following implementation was prepared from the official Swagger contract:

```text
App\Repositories\Kimia\VoucherRepository::balance()
GET /api/voucher/balance/{accountId}
includePeaks = omitted | "true" | "false"
```

Safety and behavior:

- Rejects non-positive account identifiers.
- Omits `includePeaks` when no value is requested.
- Serializes explicit booleans as Kimia-compatible literals instead of `1/0`.
- Returns the raw `BalanceDto[]` response without interpreting signs or converting Rial to
  Toman.
- Adds `kimia:inspect-balance {accountId}` as a read-only evidence command.
- Does not create, update, or delete any voucher.

Automated tests were added for the endpoint, optional query, boolean serialization, raw
negative money preservation, and invalid account identifier. Execution in the real Laravel
container is still pending and must not be reported as passed until the owner runs it.

## 21. Executable live-write safety boundary — 2026-08-03

The accepted documentation-only write restriction is now represented in code:

```text
KIMIA_WRITES_ENABLED=false
App\Integrations\Kimia\Safety\KimiaWriteGate
php artisan kimia:safety-status
```

Known write paths protected by this checkpoint:

- `KimiaService::post/put/delete`;
- non-GET requests through the public `KimiaService::client()` pending request;
- the preserved `App\Integrations\Kimia\Client\KimiaClient` write methods.

The gate fails closed when the configuration value is absent, false, or malformed. GET and
HEAD remain available. No endpoint payload, financial rule, or write permission is inferred
by this change.

The shop runner performs targeted and full automated tests, `migrate --pretend`, local
projection syncs, and the approved Balance GET in one sequence. Live reads only begin after
the local phase and `kimia:safety-status` succeed. Results are not considered verified until
the generated shop report is reviewed.

Static verification completed with 150 PHP files parsed, 75 PSR-4 declarations checked,
the PowerShell runner parsed without syntax errors, 24 changed-document links resolved,
and no changed-file Diff or secret-scan error. Laravel/Docker execution remains pending.

**End of document**
