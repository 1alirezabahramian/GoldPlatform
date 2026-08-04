#!/usr/bin/env bash
set -euo pipefail

: "${DB_HOST:?DB_HOST is required}"
: "${DB_PORT:=3306}"
: "${DB_DATABASE:?DB_DATABASE is required}"
: "${DB_USERNAME:?DB_USERNAME is required}"
: "${DB_PASSWORD:?DB_PASSWORD is required}"
: "${BACKUP_PATH:?BACKUP_PATH is required}"

mkdir -p "$(dirname "$BACKUP_PATH")"
umask 077
export MYSQL_PWD="$DB_PASSWORD"

mysqldump \
  --host="$DB_HOST" \
  --port="$DB_PORT" \
  --user="$DB_USERNAME" \
  --single-transaction \
  --quick \
  --routines \
  --triggers \
  --events \
  --set-gtid-purged=OFF \
  "$DB_DATABASE" | gzip -9 > "$BACKUP_PATH"

sha256sum "$BACKUP_PATH" > "${BACKUP_PATH}.sha256"
chmod 600 "$BACKUP_PATH" "${BACKUP_PATH}.sha256"
unset MYSQL_PWD

echo "Database backup created with checksum: $BACKUP_PATH"
