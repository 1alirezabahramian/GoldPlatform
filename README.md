# GoldPlatform

GoldPlatform is a white-label, multi-tenant platform for online gold and coin trading, customer wallets, physical delivery, pricing, accounting integrations, notifications, and operational management.

## Product Position

- **Platform:** GoldPlatform
- **Current pilot tenant:** Khalifeh Coin
- **Goal:** A reusable commercial product for jewelry stores, gold galleries, gold companies, and multi-branch businesses
- **Current status:** Backend foundation and Kimia integration in progress; frontend not started

## Architecture Direction

GoldPlatform is organized around three independent layers:

1. **Core Engine** — business rules for identity, pricing, trading, wallets, delivery, fees, limits, and audit
2. **Integrations** — Kimia, SMS, payment gateways, Telegram, pricing APIs, KYC, and future accounting connectors
3. **Branding & Tenant Configuration** — logo, colors, domains, fees, limits, branches, content, and enabled modules per customer

Customer-specific behavior must be implemented through tenant configuration, feature flags, policies, or connectors—not hardcoded into the core.

## Current Technology

- Laravel 13
- PHP 8.4
- MySQL 8.4
- Redis
- Docker Compose
- Sanctum
- Spatie Permission

Planned frontend:

- Next.js
- React
- TypeScript
- Tailwind CSS
- shadcn/ui
- TanStack Query
- Zustand
- React Hook Form
- Zod

## Documentation

- [`PROJECT_STATE.md`](PROJECT_STATE.md) — current project status and next milestone
- [`docs/00-VISION.md`](docs/00-VISION.md) — product vision and boundaries
- [`docs/decisions/ADR-001-continue-existing-repository.md`](docs/decisions/ADR-001-continue-existing-repository.md) — repository decision

## Development Principle

> Build capabilities for the gold industry, not hardcoded features for a single pilot tenant.
