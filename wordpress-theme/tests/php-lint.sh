#!/usr/bin/env bash
#
# Lint every PHP file under wordpress-theme/nsml/ with `php -l`.
# Exits non-zero if any file fails to parse.

set -uo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
THEME_DIR="$(cd "${SCRIPT_DIR}/../nsml" && pwd)"

fail=0
count=0

while IFS= read -r -d '' file; do
  count=$((count + 1))
  if ! output=$(php -l "$file" 2>&1); then
    echo "FAIL: $file"
    echo "$output"
    fail=1
  fi
done < <(find "$THEME_DIR" -type f -name '*.php' -print0)

if [ "$count" -eq 0 ]; then
  echo "No PHP files found under $THEME_DIR -- something is wrong."
  exit 1
fi

if [ "$fail" -eq 0 ]; then
  echo "PASS: php -l succeeded on all $count PHP file(s) under $THEME_DIR"
  exit 0
else
  echo "FAIL: one or more PHP files failed to lint (see above)."
  exit 1
fi
