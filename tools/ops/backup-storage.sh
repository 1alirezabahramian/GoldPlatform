#!/usr/bin/env bash
set -euo pipefail

: "${STORAGE_SOURCE:?STORAGE_SOURCE is required}"
: "${BACKUP_PATH:?BACKUP_PATH is required}"

if [[ ! -d "$STORAGE_SOURCE" ]]; then
  echo "Storage backup blocked: source directory does not exist." >&2
  exit 1
fi

mkdir -p "$(dirname "$BACKUP_PATH")"
umask 077

tar \
  --create \
  --gzip \
  --file "$BACKUP_PATH" \
  --directory "$STORAGE_SOURCE" \
  --exclude='./logs' \
  --exclude='./framework/cache' \
  --exclude='./framework/sessions' \
  --exclude='./framework/views' \
  .

sha256sum "$BACKUP_PATH" > "${BACKUP_PATH}.sha256"
chmod 600 "$BACKUP_PATH" "${BACKUP_PATH}.sha256"

echo "Storage backup created with checksum: $BACKUP_PATH"
