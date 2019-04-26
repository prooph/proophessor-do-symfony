#!/bin/sh
set -e

echo ">> Waiting for PostgreSQL to start"
WAIT=0
while ! nc -z postgres 5432; do
  sleep 1
  echo "   PostgreSQL not ready yet"
  WAIT=$(($WAIT + 1))
  if [ "$WAIT" -gt 20 ]; then
    echo "Error: Timeout when waiting for postgres socket"
    exit 1
  fi
done

echo ">> PostgreSQL socket available, resuming command execution"

"$@"
