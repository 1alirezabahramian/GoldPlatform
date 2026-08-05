# GoldPlatform — Phase 0 Auth/User Deep Comparison

> Status: Evidence Recorded — No Integration Yet
>
> Date: 2026-08-05
>
> Compared refs: `main` and `feature/goldplatform-developer-mcp`
>
> Recovery rule: Preserve → Inspect → Compare → Validate → Document → Integrate

## 1. Scope

This audit compares the live User schema, User model, User factory, registration path, OTP controller and permission trait across the two principal histories. It does not merge or modify product code.

## 2. User Schema

Both histories share the same original mobile-first `users` migration:

- `mobile` unique and required
- `name` nullable
- `national_code` nullable and unique
- `group_id` nullable
- `mobile_verified`
- `is_active`
- `last_login_at`
- remember token and timestamps

The original migration does **not** create:

- `first_name`
- `last_name`
- `email`
- `email_verified_at`
- `password`

The historical product line later adds `email`, `email_verified_at` and nullable `password` through `2026_08_04_061800_add_email_auth_columns_to_users_table.php`.

### Classification

- Original mobile-first schema: `KEEP — CANONICAL CANDIDATE`
- Later email/password migration: `DONOR — REQUIRES PRODUCT REQUIREMENT AND MIGRATION VALIDATION`
- `first_name` / `last_name`: `BROKEN CONTRACT` because no corresponding migration was found in the compared path.

## 3. User Model

### `main`

The model includes `first_name`, `last_name`, `email` and an `email_verified_at` cast even though the current `main` schema does not contain those columns. It does not use Spatie `HasRoles`.

### historical product line

The model has the same schema mismatch for `first_name` and `last_name`, while email fields are supported by the later migration. It adds Spatie `HasRoles`.

### Classification

- Mobile, name, national code, group/account links and active/verified fields: `KEEP — VERIFY`
- `first_name` and `last_name`: `BROKEN / DO NOT TRANSFER`
- Email fields: `CONDITIONAL DONOR`
- `HasRoles`: `HEALTHY DONOR — DEPENDS ON CANONICAL PERMISSION CATALOG`
- `wallet()` relation: `ARCHITECTURE DRIFT RISK`; it must not represent final Money/Gold/Coin/Currency balances.

## 4. User Factory

### `main`

The factory creates only name, email, email verification, password and remember token. It does not create the required mobile field and targets columns absent from the current `main` schema.

Status: `BROKEN`.

### historical product line

The factory creates mobile, email, password, mobile verification and active state. It is compatible only after the later email/password migration.

Status: `DONOR — REQUIRES ADAPTATION`.

### Canonical direction

A reconstructed factory must follow the selected schema exactly. For the currently accepted mobile-first baseline, it must at minimum create:

- unique valid mobile
- name
- mobile verification state
- active state
- password only if the canonical auth contract retains password login

It must not create columns merely because an old factory expected them.

## 5. Registration Service

### `main`

The service correctly converts incoming `first_name` and `last_name` values into the existing `name` column. However, it immediately creates local `RIAL` and `GOLD18` Wallet accounts with mutable balances.

Status:

- User-name normalization: `HEALTHY DONOR`
- Local financial account creation: `ARCHITECTURE DRIFT`

### historical product line

The service writes `first_name` and `last_name` directly to columns not provided by the compared migrations. It also creates local `RIAL` and `GOLD18` balance accounts.

Status: `BROKEN + ARCHITECTURE DRIFT`.

### Canonical direction

Registration must:

1. create only a user identity record matching the final schema;
2. avoid creating final customer financial balances;
3. link a Kimia account only through a separately verified workflow;
4. avoid guessing group assignment, Kimia account creation or financial mappings.

No existing RegistrationService is accepted unchanged.

## 6. OTP/Auth Controller

The compared `Api/AuthController.php` files are functionally the same. They implement OTP sending but leave `verifyOtp()` and `logout()` empty.

Status: `INCOMPLETE — NOT RELEASE READY`.

The historical product path does not provide a complete canonical authentication lifecycle merely by existing on the larger branch.

## 7. Permission Integration

The historical User model adds Spatie `HasRoles`; `main` does not.

Status: `HEALTHY DONOR`, but it must be integrated only with:

- one accepted permission catalog;
- non-destructive seeding;
- route authorization tests;
- direct-permission preservation;
- IDOR and tenant/company safety checks.

## 8. Canonical Auth/User Decision for Recovery

The recovery parent must preserve a **mobile-first identity model**. Existing email support is not rejected permanently, but it is not accepted automatically from the historical branch.

The canonical reconstruction must be a clean slice containing:

1. one users schema contract;
2. matching User model;
3. matching factory;
4. registration without Wallet balance creation;
5. completed OTP verification and logout only from existing verified behavior or separate accepted implementation;
6. permission trait and middleware only after permission consolidation;
7. migration fresh, rollback, feature, security and API-contract tests.

## 9. File Classification

| Path | main | historical product | Recovery classification |
|---|---|---|---|
| `backend/database/migrations/0001_01_01_000000_create_users_table.php` | mobile-first | same | `KEEP — VERIFY` |
| `backend/database/migrations/2026_08_04_061800_add_email_auth_columns_to_users_table.php` | absent | present | `CONDITIONAL DONOR` |
| `backend/app/Models/User.php` | schema mismatch | schema mismatch + HasRoles | `REBUILD FROM VERIFIED FIELDS` |
| `backend/database/factories/UserFactory.php` | broken | conditional donor | `REBUILD` |
| `backend/app/Services/Auth/RegistrationService.php` | partial + drift | broken + drift | `REBUILD` |
| `backend/app/Http/Controllers/Api/AuthController.php` | incomplete | incomplete | `INCOMPLETE DONOR` |

## 10. Tests Required Before Integration

- User migration fresh
- migration rollback and re-run
- factory/schema contract
- registration without local financial balance creation
- unique mobile validation
- password hashing if password remains supported
- OTP send/verify/expiry/replay/rate-limit
- logout token revocation
- role/permission boundary
- IDOR and inactive-user behavior
- full regression on exact SHA

Current execution status for this audit: `ANALYSIS EXECUTED — PRODUCT TESTS NOT EXECUTED`.

## 11. Conclusion

Neither current Auth/User implementation is canonical as-is. The original mobile-first schema is the strongest parent candidate; selected historical pieces such as `HasRoles` and parts of the factory may be adapted, while both RegistrationService variants must be rejected unchanged because they create local financial balances and one also writes nonexistent fields.
