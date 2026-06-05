#!/usr/bin/env bash
set -euo pipefail

php artisan config:clear
php artisan cache:clear
php artisan view:clear

if [[ "${RUN_MIGRATIONS:-true}" != "false" && "${RUN_MIGRATIONS:-true}" != "0" ]]; then
    php artisan migrate --force
else
    echo "Skipping migrations because RUN_MIGRATIONS=${RUN_MIGRATIONS:-false}"
fi

php artisan storage:link || true
php artisan config:cache
php artisan view:cache
