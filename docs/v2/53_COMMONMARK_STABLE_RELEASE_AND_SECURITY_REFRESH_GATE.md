# V2-00 — CommonMark Stable Release and Security Refresh Gate

Date: 2026-08-07

## Status

The prior external blocker for PR #196 has changed state.

Previous state:

`BLOCKED BY UPSTREAM STABLE SECURITY RELEASE`

Current upstream evidence:

- Packagist publishes stable `league/commonmark 2.9.0`.
- The package page identifies `2.9.0` as a stable release.
- The historical failing GoldPlatform lock state used `league/commonmark 2.8.3`.

Therefore the external-release blocker is cleared, but PR #196 is still **NOT READY TO MERGE**.

## Current required action

The repository still needs a resolver-generated dependency refresh so that `backend/composer.lock` resolves the patched stable version.

Required path:

1. Use Composer's resolver on a narrow dependency-security branch based on the operational branch.
2. Do not hand-edit `composer.lock`.
3. Do not use `2.9.x-dev` or another development alias.
4. Do not suppress Composer advisories or weaken Security Hardening.
5. Run exact-head Security Hardening and Backend RC2 Candidate after the resolver-generated lock update.
6. Keep Kimia read-only / write-disabled safety boundaries unchanged.
7. Only after all exact-SHA security and regression gates pass may the dependency refresh be considered for integration.
8. PR #196 must then be refreshed against the corrected operational base without shared-history rebase and must rerun exact-head CI before any merge decision.

## Duplicate audit

Repository search found no existing CommonMark security-refresh PR and no existing `composer update league/commonmark` path to reuse.

Classification: `NO DUPLICATE CANDIDATE FOUND`.

## Safety boundaries

No financial rule, Kimia mapping, Kimia Write payload, migration, customer balance behavior, API contract, permission model, or frontend behavior is authorized by this dependency-security gate.

Manual lockfile editing, advisory suppression, development-version substitution, or merging PR #196 with red security CI remain prohibited.

## V2-00 impact

This removes an external waiting condition and converts it into an internal dependency-refresh execution gate.

The V2-00 stage remains:

`GATE NOT PASSED`
