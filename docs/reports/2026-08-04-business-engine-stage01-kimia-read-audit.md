# GoldPlatform — Stage 01 Kimia Read-Only Audit

**Date:** 2026-08-04  
**Branch:** `work/business-engine-stage01-kimia-read`  
**Status:** Audit Started

## Objective

Create one canonical, testable and read-only Kimia integration path before any Trading or Financial Engine work.

## Confirmed Scope

- Account Groups
- Accounts
- Coins
- Currencies
- Balance
- Read-only error handling
- HTTP fake contract tests
- Header and query parameter documentation

## Out of Scope

- Account creation or update
- Voucher write
- Buy/Sell document payloads
- Action Code mapping
- Financial formulas
- Hard-coded Coin or Currency identifiers
- Live Kimia calls in CI

## Initial Findings

### Current Client

`backend/app/Clients/KimiaClient.php`

- Provides GET, POST, PUT and DELETE in one client.
- Uses retry for every HTTP method.
- Reads the base URL from `services.kimia.url`.
- Does not show a centralized `X-Book-Id` header in the inspected code.
- Logs method, URI and response status.

### Current Account Repository

`backend/app/Repositories/Kimia/AccountRepository.php`

- Mixes read and write operations.
- Uses `accountType` for `/api/account`; the accepted project evidence indicates this endpoint must be verified with `Type=3` before implementation.
- Returns an empty array for failed HTTP responses, making an API error indistinguishable from a valid empty result.
- Contains create and update methods that are outside Stage 01.

## Safety Decisions for This Stage

- No endpoint parameter will be changed without Swagger or real API evidence.
- No live write request will be executed.
- Read contract tests will use Laravel HTTP fakes.
- Failed responses must not silently become valid empty business data.
- Coin and Currency data must remain dynamic.

## Next Implementation Slice

1. Audit configuration keys and headers.
2. Add a dedicated read-only boundary or guard.
3. Add HTTP fake tests for Account Groups and Accounts.
4. Add typed failure behavior without inventing business rules.
5. Continue with Coins, Currencies and Balance only after tests pass.
