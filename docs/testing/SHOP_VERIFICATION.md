# GoldPlatform — Shop Verification Runbook

Status: Prepared; first execution pending

Date: 2026-08-03

## Goal

Run the complete Docker/Laravel checkpoint, safe migration preview, and approved live
Kimia reads in sequence while collecting console output in one shareable text file.

## Command

Run from the GoldPlatform repository root on the shop computer:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\run-shop-verification.ps1 -IncludeLiveKimia -AccountId 350
```

The script creates:

```text
test-reports/shop-verification-YYYYMMDD-HHMMSS.txt
```

`test-reports/` is ignored by Git and the resulting report should be sent directly for
review, not committed.

## Preconditions and Automatic Stops

The script stops before tests when:

- the current branch is not `work/product-kimia-next`;
- tracked local changes exist;
- Git or the repository cannot be read.

The live Kimia phase is skipped when any local verification step fails or
`kimia:safety-status` detects that writes are enabled.

## Local Verification Phase

The report includes, in order:

1. Git status and current branch.
2. Docker Compose service state.
3. PHP and Laravel environment information.
4. Runtime Kimia write-safety status.
5. Targeted write-gate tests.
6. Targeted identity/account-binding tests.
7. Targeted Tenant root/domain/isolation tests.
8. Targeted Balance repository and command tests.
9. Targeted local sync-state command tests.
10. Full automated Laravel test suite.
11. Current migration status.
12. SQL preview of pending migrations using `migrate --pretend`.

`migrate --pretend` displays the pending SQL and preflight result; it does not apply a
Migration to the shop database.

## Approved Live Read-only Phase

This phase runs only after the local phase succeeds and `-IncludeLiveKimia` is present:

1. Read-only Kimia connection check.
2. Account-group GET and local projection update.
3. Retail-account GET (`Type=3`) and local projection update.
4. Coin GET and local projection update.
5. Currency GET and local projection update.
6. Local verification that `AccountId=350` exists in `external_accounts`.
7. Read-only Balance request for `AccountId=350`.

Account synchronization writes only to GoldPlatform's local projection. It does not write
to Kimia. The report omits customer name, mobile, national code, and raw account payload.
The Balance command also omits account names by default.

## Explicitly Excluded

- `php artisan migrate` without `--pretend`.
- Kimia `POST`, `PUT`, or `DELETE`.
- Voucher creation, modification, reversal, or deletion.
- Account creation or update in Kimia.
- Display of `.env`, credentials, tokens, passwords, or full customer payloads.

## Review Rule

No pending Migration is applied and no additional Kimia behavior is implemented until the
generated report is reviewed. A successful previous suite does not count as verification
of this new checkpoint.
