# Stage 17 — Backup and Disaster Recovery

Implemented:

- transactional MySQL backup script using `mysqldump --single-transaction`;
- SHA-256 checksum generation and verification;
- guarded restore script requiring `ALLOW_RESTORE=true`;
- restore target naming guard: only databases ending in `_restore` or `_drill` are accepted;
- application storage archive with volatile cache/session/view/log directories excluded;
- isolated GitHub Actions drill that creates source data, backs it up, restores it into a separate database and verifies the restored marker;
- drill evidence artifact retained for 30 days.

## Safety boundaries

- scripts do not contain credentials;
- passwords are passed through environment variables and `MYSQL_PWD`, not command-line arguments;
- the restore script does not create, drop or overwrite a database;
- the target database must already exist and must be explicitly named as an isolated restore/drill database;
- production recovery is not claimed until the same runbook is executed against the approved target infrastructure and backup storage.

## Operational sequence

1. create encrypted off-host backup destination;
2. run database and storage backups;
3. verify checksums;
4. provision an isolated restore database;
5. restore with the explicit guard enabled;
6. verify critical record counts, migration state and financial reconciliation;
7. record evidence and recovery duration;
8. never promote restored data without owner-approved reconciliation.
