# GoldPlatform
## Project State

آخرین بروزرسانی:
2026-07-18

---

# وضعیت کلی پروژه

Progress : حدود 25%

Status : Phase 2 Started

---

# زیرساخت

✅ Git Repository

✅ Docker

✅ Docker Compose

✅ Laravel 12

✅ MySQL

✅ Redis

✅ Nginx

✅ PHP 8.4

---

# Authentication

✅ OTP Login

✅ OTP Verification

✅ Sanctum Token

✅ Auto User Creation

✅ Login By Mobile

✅ Last Login Update

---

# Database

Completed

- Users
- OTP
- Wallets
- Wallet Transactions
- User Groups
- Personal Access Tokens
- Account Groups

---

# Kimia Integration

Completed

✅ Connection

✅ Authentication

✅ Health Check

✅ SDK Layer

✅ Repository Layer

✅ Account Groups Sync

---

# Synced From Kimia

Account Groups

10 Groups imported successfully.

---

# Architecture

GoldPlatform

↓

Controllers

↓

Services

↓

Repositories

↓

Kimia SDK

↓

Kimia Accounting API

---

# Current APIs

Working

POST /api/auth/login

POST /api/auth/verify-otp

GET /api/account/groups

---

# Next Sprint

Phase 2

Account Synchronization

Tasks

- Sync Accounts
- Sync Customers
- Sync Banks
- Sync Employees
- Save Kimia AccountId
- Account Search
- Local Cache

---

Upcoming

Balance Sync

Wallet Sync

Product Sync

Trading Engine

Operator Panel

Notification Center

---

Health

Docker

Healthy

Laravel

Healthy

Database

Healthy

Redis

Healthy

Kimia API

Healthy

Authentication

Healthy

Overall

Stable

## 2026-07-21

### Completed
- RegistrationService implemented
- Wallet created automatically after registration
- Sanctum token generation verified
- Kimia API connection verified
- AccountGroup model integrated
- Authentication routes verified
- Database migrations completed
- Docker environment healthy

### Known Issues
- Registration endpoint returns HTTP 500 only when accessed through Nginx.
- Registration works correctly when executed directly inside Laravel/container.
- Root cause appears to be Nginx/PHP-FPM integration and will be investigated later.

### Next Tasks
1. Complete project architecture audit
2. Fix Nginx registration issue
3. Kimia account creation
4. Wallet services
5. Orders module

## 2026-07-21

### Completed
- Registration API completed
- Wallet auto creation completed
- Sanctum token generation verified
- AccountGroup model connected
- Kimia connection verified
- Health Check passed

### Pending
- Fix Windows -> Nginx -> PHP POST 500 issue
- Kimia Account auto creation
- OTP Login completion


# GoldPlatform Project State

**Last Update:** 2026-07-22
**Branch:** main
**Repository Status:** Synced with GitHub

---

## Current Project Status

GoldPlatform is currently in the Backend Foundation phase.

The core Laravel infrastructure, Docker environment, database foundation, authentication base, permission structure, and initial wallet architecture have been implemented and committed.

---

# Completed Milestones

## Infrastructure

✅ Laravel project initialized
✅ Docker environment configured

Current services:

* Nginx
* PHP-FPM Laravel container
* MySQL 8.4
* Redis

---

## Database Foundation

Completed:

✅ Base migrations structure
✅ User and permission foundation
✅ Wallet database architecture

Implemented wallet tables:

* wallets
* wallet_accounts
* wallet_transactions

---

## Wallet Architecture

Completed:

✅ WalletAccount model
✅ WalletTransaction model
✅ WalletAccountType enum
✅ Wallet service foundation
✅ Wallet transaction structure

Purpose:

The wallet system will support:

* Gold balance
* Toman balance
* Customer transactions
* Buy/Sell operations
* Future accounting integration

---

# Documentation

Updated:

✅ ARCHITECTURE_BLUEPRINT.md

Documentation now includes:

* System architecture direction
* Wallet design decisions
* Development structure

---

# Git History

Latest successful push:

Commit:

`966d142`

Status:

```
main branch synced
working tree clean
```

---

# Current Development Focus

Next steps:

1. Run and verify wallet migrations
2. Test database relationships
3. Complete Wallet Service logic
4. Connect wallet flow to Buy/Sell order system
5. Prepare integration layer for Kimia accounting API

---

# Important Development Rule

Development workflow:

Design → Code → Test → Commit → Update project_state.md

Every major milestone must be documented before moving to the next phase.

---

# Current Risk Points

* Redis PHP extension inside container needs verification
* Permission migration requires final validation
* Wallet relations need automated tests before production flow

---

# Next Checkpoint

After wallet verification:

Create commit:

```
test: verify wallet database structure and application health
```

Then continue toward order and trading flow implementation.

# GoldPlatform Project State

**Last Update:** 2026-07-22
**Branch:** main
**Repository:** GitHub - GoldPlatform
**Status:** Stable Development Checkpoint

---

# Project Overview

GoldPlatform is an online gold trading platform designed to support:

* Gold buying and selling
* Customer wallet management
* Accounting integration with Kimia
* Secure authentication
* Order management
* Digital and physical gold operations

Current development phase:

**Backend Foundation → Wallet & Transaction Core**

---

# Infrastructure Status

## Docker Environment

Status: ✅ Healthy

Active Containers:

| Service     | Status    |
| ----------- | --------- |
| Nginx       | ✅ Running |
| PHP-FPM 8.4 | ✅ Running |
| MySQL 8.4   | ✅ Running |
| Redis       | ✅ Running |

---

# Laravel Environment

Framework:

```
Laravel 13.20.0
PHP 8.4
```

Status:

✅ Laravel boot successful
✅ Artisan commands working
✅ Cache optimization successful

---

# PHP Extensions

Required Laravel extensions:

| Extension | Status  |
| --------- | ------- |
| PDO       | ✅       |
| PDO MySQL | ✅       |
| ZIP       | ✅       |
| MBString  | ✅       |
| INTL      | ✅ Added |

Latest fix:

```
fix: add php intl extension for laravel support
```

Commit:

```
fc1ca22
```

---

# Database Status

Database:

```
MySQL 8.4.10
Database: goldplatform
Tables: 30
```

Migration Status:

✅ All migrations completed successfully

---

# Database Modules Completed

## Authentication & Users

Completed:

* Users table
* OTP foundation
* Personal access tokens
* User groups
* Account structure

---

## Permission System

Completed:

* Spatie Permission tables
* Roles foundation
* Permissions foundation

---

## Wallet System

Status: ✅ Foundation Completed

Implemented:

### Wallet

* Wallet model
* Wallet table

### Wallet Accounts

Added:

```
wallet_accounts
```

Features:

* Multiple account types
* Separation of balances
* Future support for:

  * Gold balance
  * Toman balance
  * Customer accounts

### Wallet Transactions

Implemented:

* Transaction model
* Transaction structure
* Account relation support

---

# Wallet Architecture Files

Added:

```
app/
 ├── Enums/
 │    └── WalletAccountType.php
 │
 ├── Models/
 │    ├── Wallet.php
 │    ├── WalletAccount.php
 │    └── WalletTransaction.php
 │
 └── Services/
      └── Wallet/
```

---

# Documentation Status

Updated:

```
docs/ARCHITECTURE_BLUEPRINT.md
docs/project_state.md
```

Documentation includes:

* Architecture decisions
* Current implementation state
* Development checkpoints

---

# Git Status

Current branch:

```
main
```

Repository status:

```
Your branch is up to date with origin/main.
Working tree clean.
```

Latest commits:

```
fc1ca22 fix: add php intl extension for laravel support

966d142 docs: document Laravel setup, database migration progress, and project decisions
```

---

# Health Check Report

Date:

```
2026-07-22
```

Result:

```
Infrastructure        ✅ PASS
Laravel               ✅ PASS
Database              ✅ PASS
Migration System      ✅ PASS
Wallet Foundation     ✅ PASS
PHP Extensions        ✅ PASS
Git Backup            ✅ PASS
```

---

# Current Risks / Pending Items

## Pending Verification

* Wallet model relationships
* Wallet service business logic
* Transaction validation rules
* Automated tests

---

# Next Development Phase

## Wallet Business Logic

Tasks:

1. Complete Wallet Service
2. Implement deposit/withdraw operations
3. Add balance validation
4. Create transaction workflows
5. Connect Wallet with Order system

---

# Development Rule

Every major change follows:

```
Design
↓
Implementation
↓
Health Check
↓
Documentation Update
↓
Git Commit
↓
Push
```

---

# Current Stable Checkpoint

GoldPlatform is now at:

```
Backend Foundation Complete
+
Wallet Architecture Ready
+
Infrastructure Stable
```

Next milestone:

```
Wallet Transaction Engine
```

---

# Stabilization Checkpoint — 2026-08-02

## Scope

Kimia account synchronization and backend duplicate-path audit.

## Completed in code

- Corrected `GET /api/account` query from `accountType` to Swagger-defined `Type`.
- Kept `accountType` only for `GET /api/account/groups`.
- Confirmed the repeatable, validated `--type` option in `kimia:sync-accounts`; without an option it synchronizes all defined `AccountType` cases.
- Confirmed mapping of Swagger-defined account fields into `external_accounts`.
- Fixed `kimia:sync-groups` to call the canonical repository method.
- Removed legacy duplicate Kimia paths under `app/Clients` and `app/Services/kimia`; preserved the pre-existing `App\Integrations\Kimia` tree pending a separate architecture review.
- Consolidated Kimia configuration under `config/services.php` and documented non-secret environment placeholders in `.env.example`.
- Added repository tests that lock the two different query parameter names.

## Test status

- Static source verification: completed.
- Laravel/PHP automated tests: pending because the current Codex runtime does not contain PHP, Composer, or Docker.
- Live Kimia account sync: pending; no Kimia credentials were available in the runtime and no credential was copied into source.

## Remaining blockers

- Numeric trade Action encoding is not runtime-confirmed: the owner-confirmed operational/form codes are `3/4`, while Swagger API schemas define `32/64`.
- The complete Kimia voucher write payload, retry behavior, and verified write flow are not yet confirmed. Swagger does confirm `RequestId` UUID support for idempotency.
- Live Kimia account sync remains pending because credentials are not available in this runtime.
- Authentication decisions below remain unresolved and separate from the Kimia transaction mapping.

## Confirmed customer trade mapping — 2026-08-02

- Customer Buy in GoldPlatform maps semantically to the business selling to the customer in Kimia.
- Customer Sell in GoldPlatform maps semantically to the business buying from the customer in Kimia.
- Added `App\Enums\KimiaTradeSide` as the numeric-free code-level mapping contract.
- Added unit tests for both directions and rejection of unsupported order types.
- Added accepted decision record `ADR-023` and reconciled the conflicting Ground Truth and Business Rules sections.
- No live Kimia financial write was enabled in this step.
- Numeric API Action remains blocked pending real transaction evidence (`3/4` operational codes versus Swagger `32/64`).
- Added read-only `VoucherRepository::transactions()` using the exact Swagger endpoint and query names.
- Added `kimia:inspect-transactions {accountId}` to display the evidence fields needed for Action verification without mutating Kimia.
- Added HTTP-fake tests for the transaction path, zero-based pagination, descending order, and pass-through of raw Action values.
- Owner confirmed `AccountId=350` as the read-only evidence account. No live response has
  been captured in the current Codex runtime, so the `3/4` versus `32/64` transport mapping
  remains intentionally unresolved.

## Authentication/SMS structural follow-up

- Fixed five confirmed PSR-4 path/class mismatches.
- Added missing request, provider contract, and result DTO used by the active OTP route.
- Removed unused duplicate Auth/OTP/SMS implementations from the application path.
- Added an automated PSR-4 compliance test to prevent recurrence.
- OTP storage security and verification behavior were not redesigned in this pass and remain pending explicit review.

## Confirmed authentication blockers

- OTP-only project rules conflict with the password field currently required by registration code.
- Registration writes `first_name` and `last_name`, but those columns do not exist in the current migration chain.
- The existing wallet observer test expects nine accounts although the observer is empty and registration creates two accounts.
- Active OTP verification and logout methods are incomplete.

These are documented stop conditions for the next authentication implementation pass.
