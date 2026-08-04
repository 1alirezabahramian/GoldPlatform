#!/usr/bin/env bash
set -euo pipefail

: "${TARGET_URL:?TARGET_URL is required}"
REQUESTS="${REQUESTS:-100}"
CONCURRENCY="${CONCURRENCY:-10}"
MAX_P95_SECONDS="${MAX_P95_SECONDS:-1.000}"
RESULTS="$(mktemp)"
trap 'rm -f "$RESULTS"' EXIT

export TARGET_URL RESULTS
seq "$REQUESTS" | xargs -P "$CONCURRENCY" -I{} sh -c '
  curl --silent --show-error --output /dev/null \
    --write-out "%{http_code} %{time_total}\n" \
    --max-time 10 "$TARGET_URL" >> "$RESULTS"
'

completed="$(wc -l < "$RESULTS" | tr -d ' ')"
non_success="$(awk '$1 != 200 {count++} END {print count+0}' "$RESULTS")"
index="$(( (completed * 95 + 99) / 100 ))"
p95="$(awk '{print $2}' "$RESULTS" | sort -n | awk -v target="$index" 'NR == target {print; exit}')"

printf 'requests=%s completed=%s non_success=%s p95_seconds=%s\n' "$REQUESTS" "$completed" "$non_success" "$p95"

test "$completed" -eq "$REQUESTS"
test "$non_success" -eq 0
awk -v p95="$p95" -v max="$MAX_P95_SECONDS" 'BEGIN { exit !(p95 <= max) }'
