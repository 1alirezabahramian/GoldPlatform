# GoldPlatform — Phase 0 Kimia Deep Comparison

> Status: Evidence Recorded — No Integration Yet
>
> Date: 2026-08-05
>
> Compared refs: `main` and `feature/goldplatform-developer-mcp`

## 1. Ground Truth

Kimia is the final source of truth for Money, Gold, Coin and Currency. GoldPlatform may cache or project Kimia data only for performance, audit, workflow and reconciliation; it must not create a competing final balance.

Kimia read and write paths must remain separate. Write stays deny-by-default until real payload and mapping Ground Truth are verified and approved.

## 2. `main` Kimia Client

`backend/app/Clients/KimiaClient.php`:

- exposes GET, POST, PUT and DELETE through one client;
- applies the same retry behavior to all methods;
- has no central read-only write guard;
- does not enforce failure handling;
- logs method, URI and status;
- uses `services.kimia.url` without the stronger configuration validation present in the historical path.

Classification: `LEGACY / UNSAFE AS CANONICAL`.

## 3. `main` Account Repository

`backend/app/Repositories/Kimia/AccountRepository.php`:

- mixes read and write operations in one repository;
- uses `accountType` for `/api/account`, while verified project Ground Truth requires `Type`;
- converts failed reads to empty arrays or null, hiding operational failures;
- directly exposes create and update methods.

Classification: `BROKEN CONTRACT + READ/WRITE MIXING`.

It must not be transferred unchanged into the canonical baseline.

## 4. Historical Integration Client

`backend/app/Integrations/Kimia/Client/KimiaClient.php`:

- centralizes Kimia access under one integration namespace;
- validates required configuration;
- uses Basic Authentication from configuration;
- defaults to read-only mode;
- blocks non-GET methods before an HTTP request leaves the application;
- retries GET requests only;
- does not automatically retry writes;
- provides endpoint timeout profiles;
- raises a domain-specific `KimiaException` on HTTP or connection failure;
- logs operational metadata without logging credentials or payloads.

Classification: `STRONG CANONICAL DONOR — REQUIRES CONTRACT TEST RE-EXECUTION`.

## 5. Historical Account Repository

`backend/app/Integrations/Kimia/Repositories/KimiaAccountRepository.php`:

- is read-only;
- uses `Type` correctly for `/api/account`;
- maps external rows to explicit DTOs;
- does not expose account creation or update;
- keeps response-shape normalization inside the integration boundary.

A remaining inconsistency exists: `groups()` still uses `accountType` for `/api/account/groups`. The uploaded project Ground Truth confirms `Type` for `/api/account`; it does not, by itself, prove the correct filter name for every other endpoint. Therefore this groups filter must be verified against Swagger or real API evidence before canonical acceptance.

Classification: `HEALTHY DONOR WITH ONE VERIFICATION GAP`.

## 6. Canonical Direction

The final Kimia slice should be reconstructed from the historical `App\Integrations\Kimia` path, not by retaining the legacy `App\Clients`, `App\Repositories\Kimia`, `App\Services\kimia` and parallel service trees.

Required canonical components:

1. one central read-only client;
2. explicit read repositories and DTO/mappers;
3. no Controller-to-client direct calls;
4. separate disabled write-preparation boundary;
5. GET-only retries with bounded delays;
6. exact decimal/string handling;
7. dynamic Coin and Currency catalogs;
8. failure transparency rather than converting errors to empty balances;
9. balance re-read from Kimia after any future approved write;
10. no credentials, tokens or payloads in logs.

## 7. File Classification

| Path | Classification |
|---|---|
| `backend/app/Clients/KimiaClient.php` | `LEGACY — SUPERSEDE` |
| `backend/app/Repositories/Kimia/AccountRepository.php` | `BROKEN / DO NOT TRANSFER` |
| `backend/app/Services/kimia/*` | `PARALLEL LEGACY PATH — SUPERSEDE AFTER DEPENDENCY CHECK` |
| `backend/app/Integrations/Kimia/Client/KimiaClient.php` | `STRONG CANONICAL DONOR` |
| `backend/app/Integrations/Kimia/Repositories/KimiaAccountRepository.php` | `HEALTHY DONOR — VERIFY GROUP FILTER` |
| `backend/app/Integrations/Kimia/Repositories/VoucherRepository.php` | `DONOR — VERIFY AGAINST REAL BALANCE RESPONSE` |
| `backend/app/Integrations/Kimia/DTO/*` | `DONOR — CONTRACT TEST REQUIRED` |
| `backend/app/Integrations/Kimia/Mappers/*` | `DONOR — CONTRACT TEST REQUIRED` |
| `backend/app/Integrations/Kimia/Write/*` | `PRESERVE DENY-BY-DEFAULT — DO NOT ACTIVATE` |

## 8. Tests Required Before Integration

- configuration failure tests
- GET account with `Type=3`
- account response mapping
- voucher balance mapping including positive, zero and negative values
- dynamic coin and currency catalogs
- connection and HTTP failure transparency
- read retry count and delay
- proof that POST/PUT/DELETE never leave the process in read-only mode
- proof that write methods are not retried
- secret/log redaction
- Controller architecture guard
- exact-SHA full regression
- optional live read-only validation only in an approved connected environment

Current status: `ANALYSIS EXECUTED — PRODUCT TESTS NOT EXECUTED`.

## 9. Conclusion

The historical `App\Integrations\Kimia` implementation is substantially safer and closer to the accepted architecture than the current legacy `main` paths. It is the leading donor for the canonical Kimia read slice, but it must be reconstructed on the final parent branch with exact contract tests and without directly merging the historical branch.
