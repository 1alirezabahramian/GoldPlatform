# Backup and Restore Runbook

## Preconditions

- approved backup destination with encryption and restricted access;
- database credentials supplied only through the secret store;
- enough free space for database and storage archives;
- isolated restore database already provisioned;
- Kimia write remains disabled during recovery verification.

## Database backup

```bash
DB_HOST=... DB_PORT=3306 \
DB_DATABASE=... DB_USERNAME=... DB_PASSWORD=... \
BACKUP_PATH=/secure/path/goldplatform-$(date +%Y%m%d%H%M%S).sql.gz \
tools/ops/backup-database.sh
```

Verify the generated `.sha256` file before copying or restoring.

## Storage backup

```bash
STORAGE_SOURCE=/path/to/backend/storage \
BACKUP_PATH=/secure/path/goldplatform-storage-$(date +%Y%m%d%H%M%S).tar.gz \
tools/ops/backup-storage.sh
```

## Isolated restore

The target database name must end in `_restore` or `_drill`.

```bash
ALLOW_RESTORE=true \
DB_HOST=... DB_PORT=3306 \
DB_DATABASE=goldplatform_restore \
DB_USERNAME=... DB_PASSWORD=... \
BACKUP_PATH=/secure/path/backup.sql.gz \
tools/ops/restore-database.sh
```

## Verification checklist

- checksum passes;
- expected tables exist;
- migration status is reviewed;
- user/order/ledger/settlement/custody record counts are compared;
- financial reconciliation is executed before promotion;
- no real Kimia write request is allowed during verification;
- recovery duration and evidence are recorded.

## Prohibited actions

- restore directly into the active production database;
- disable checksum verification;
- put database passwords in scripts, Git, issue comments or logs;
- declare recovery successful without financial reconciliation.
