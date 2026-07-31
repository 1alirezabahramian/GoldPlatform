# GoldPlatform — Project State

**Last updated:** 2026-07-31  
**Status:** Active development  
**Repository:** `1alirezabahramian/GoldPlatform`  
**Current branch for documentation work:** `docs/product-foundation`

## Product Definition

GoldPlatform is a white-label, multi-tenant platform for online gold and coin trading, customer wallets, custody, delivery, pricing, accounting integrations, notifications, and operational management.

Khalifeh Coin is the first real pilot tenant and validation environment; it is not the permanent identity of the core product.

## Current Architecture Direction

- Core business engine independent from customer-specific rules
- Tenant isolation through `tenant_id`
- White-label branding and tenant configuration
- External systems accessed through adapters/connectors
- Kimia treated as one accounting connector, not the platform domain
- Complex backend, simple frontend
- No guessing in financial or Kimia-related behavior

## Verified Completed Foundations

### Infrastructure

- Docker-based development environment
- Nginx, PHP-FPM, MySQL, and Redis containers
- Laravel backend running
- Git and GitHub repository configured

### Authentication and Identity

- Registration
- Login
- Sanctum authentication
- OTP-related tables
- User model foundation
- Authentication routes and services

### Authorization

- Spatie Permission installed
- Permission migrations available
- Initial user-group concepts documented

### Wallet Foundation

- Wallet model and migration
- WalletAccount model and migration
- WalletTransaction model and migration
- Registration flow creates wallet foundation

### Kimia Discovery and Audit

- Swagger reviewed
- Runtime logs reviewed
- Account, Product, Coin, Currency, Voucher Balance, and selected transaction endpoints identified
- Confirmed account filter parameter: `Type=3` for retail accounts
- Kimia integration boundary and adapter requirement documented
- Duplicate or overlapping Kimia classes identified for later controlled cleanup

### Documentation

- Project memory and architecture ground truth exist in Library
- Kimia integration audit exists in Library
- Repository README foundation created on `docs/product-foundation`

## Current In-Progress Areas

### Kimia Integration

Status: **In progress / current technical priority**

Known work:

- Verify the single active Kimia client/service path
- Stabilize account synchronization using real API output
- Confirm account group synchronization
- Map balances without guessing sign or product semantics
- Extract remaining request and response schemas
- Add DTOs, mappers, repositories, exceptions, and idempotency
- Add mock integration tests and controlled live tests

### Product Architecture Refactor

Status: **Approved direction, not fully implemented**

Required:

- Introduce Tenant domain and tenant isolation
- Separate GoldPlatform domain models from Kimia DTOs
- Move customer-specific rules to tenant settings or policies
- Define feature flags and licensing capabilities
- Prevent Khalifeh-specific behavior from entering the core

## Not Started or Not Complete

- Frontend application
- Admin panel
- Operator panel
- Customer panel
- Complete product catalog module
- Production-grade pricing engine
- Trading order lifecycle
- Approval/rejection workflow
- Price freeze workflow
- Full wallet ledger and settlement guarantees
- Physical custody/Amanat workflow
- Delivery workflow
- Multi-branch operations
- KYC integration
- Payment gateway integration
- Telegram integration inside the platform
- Reporting and analytics
- Licensing and plan enforcement
- SaaS tenant onboarding
- Production deployment and monitoring

## Ground-Truth Domain Model

### Financial Assets

- Money
- Gold
- Coin
- Currency

These balances may be positive, zero, or negative based on tenant rules and credit permissions.

### Physical Custody Assets

- Parsian
- Bullion
- Melted gold products
- Jewelry and future physical products

Physical custody must remain separate from financial balances.

### Confirmed Kimia Action Codes

- `1` — Receive
- `2` — Pay
- `3` — Buy
- `4` — Sell
- `7` — Transfer
- `8` — Coin/currency monetization or conversion mapping

Raw Kimia codes must remain inside the integration boundary.

## Current Blockers and Risks

1. Kimia account synchronization has previously returned zero local records.
2. Multiple duplicate Kimia client/service/repository implementations exist.
3. Some older project documents contain conflicting assumptions.
4. Paper-gold action direction must be verified end-to-end against real Kimia behavior before implementation.
5. Multi-tenancy has been accepted architecturally but is not yet implemented in existing tables.
6. Frontend has not started, so no end-to-end customer workflow exists.

## Immediate Next Milestone

### Milestone: Product Foundation and Kimia Stabilization

1. Consolidate project documentation and ADRs.
2. Verify the active backend and Kimia code paths.
3. Stabilize Account and AccountGroup synchronization.
4. Verify Balance mapping from real responses.
5. Create connector contracts and MockConnector.
6. Design Tenant and tenant-scoping strategy.
7. Only then begin the Trading and Frontend implementation.

## Development Rules

- No guessing
- No silent architecture changes
- No customer-specific business logic in the core
- No raw Kimia DTOs or codes outside the integration layer
- Every important decision must be documented
- Every completed phase must include tests and a health check
