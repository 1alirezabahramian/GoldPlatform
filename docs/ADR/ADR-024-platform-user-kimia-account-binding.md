# ADR-024 — Platform User to Kimia Account Binding

Status: Accepted (database enforcement pending)

Date: 2026-08-03

## Context

A real customer may request more than one independent account. GoldPlatform must not
represent those accounts as a selectable list of Kimia identifiers under one login,
because that would make authorization, balance ownership, audit trails, and financial
posting ambiguous.

The current model already expresses `User belongsTo Account` and `Account hasOne User`,
but the database does not yet enforce the reverse uniqueness of `users.account_id`.

## Decision

- One GoldPlatform login/account is connected to no more than one local `accounts` row
  and therefore no more than one Kimia `AccountId`.
- One Kimia `AccountId` must not be connected to more than one GoldPlatform login/account.
- If the same real customer requests a second account, a second GoldPlatform account is
  created with a different mobile number and a different Kimia `AccountId`.
- GoldPlatform will not add account switching or multiple Kimia `AccountId` values to one
  authenticated user.
- `AccountId` remains the stable Kimia integration identifier. `account_code` remains a
  separate display/search value and must not replace it.

This decision was confirmed by the project owner on 2026-08-03.

## Current Constraint Audit

| Contract | Current implementation | Status |
|---|---|---|
| One platform account per mobile number | `users.mobile UNIQUE` | Enforced |
| One local account per Kimia identifier | `accounts.kimia_id UNIQUE` | Enforced |
| One Kimia/local account per platform user | Nullable `users.account_id` foreign key | Structurally represented |
| One platform user per Kimia/local account | `Account::user()` is `HasOne`, but `users.account_id` is not unique | Not database-enforced |

## Unresolved Identity Constraint

The current schema and registration validation also require `users.national_code` to be
unique. A second account for the same physical person would normally reuse the same
national code and would therefore conflict with the current constraint.

This ADR does not guess how KYC identity should behave. Before changing the schema, the
project owner must confirm whether two accounts belonging to the same person may share a
national code, and how Jibit/KYC approval should be represented.

## Consequences

- Authorization and every financial operation can resolve exactly one Kimia customer
  account from the authenticated platform account.
- Mobile numbers and Kimia `AccountId` values for the two accounts must both be distinct.
- A future migration should add a nullable unique constraint to `users.account_id`, but
  only after a duplicate-data preflight and a successful migration test in the shop
  Docker runtime.
- Account-linking services must reject an already-linked `AccountId` transactionally and
  record the action in the audit log.
- No database migration or runtime behavior is changed by this documentation checkpoint.

## Scope Boundary

This ADR defines the account-binding cardinality only. It does not define shared national
code behavior, household/business relationships, account delegation, operator access,
KYC reuse, or Kimia account-creation APIs.
