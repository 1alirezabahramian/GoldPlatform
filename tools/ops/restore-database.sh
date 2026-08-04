#!/usr/bin/env bash
set -euo pipefail

: "${ALLOW_RESTORE:?ALLOW_RESTORE must be explicitly set}"
: "${DB_HOST:?DB_HOST is required}"
: "${DB_PORT:=3306}"
: "${DB_DATABASE:?DB_DATABASE is required}"
: "${DB_USERNAME:?DB_USERNAME is required}"
: "${DB_PASSWORD:?DB_PASSWORD is required}"
: "${BACKUP_PATH:?BACKUP_PATH is required}"

if [[ "$ALLOW_RESTORE" != "true" ]]; then
  echo "Restore blocked: ALLOW_RESTORE must equal true." >&2
  exit 1
fi

if [[ ! "$DB_DATABASE" =~ (_restore|_drill)$ ]]; then
  echo "Restore blocked: target database name must end with _restore or _drill." >&2
  exit 1
fi

if [[ ! -f "$BACKUP_PATH" || ! -f "${BACKUP_PATH}.sha256" ]]; then
  echo "Restore blocked: backup or checksum file is missing." >&2
  exit 1
fi

sha256sum --check "${BACKUP_PATH}.sha256"
export MYSQL_PWD="$DB_PASSWORD"
gzip --decompress --stdout "$BACKUP_PATH" | mysql \
  --host="$DB_HOST" \
  --port="$DB_PORT" \
  --user="$DB_USERNAME" \
  "$DB_DATABASE"
unset MYSQL_PWD

echo "Database restore completed into isolated target: $DB_DATABASE"
