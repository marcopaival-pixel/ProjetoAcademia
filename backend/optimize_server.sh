#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

echo "=== NexShape optimize (Linux) ==="

composer install --no-dev --optimize-autoloader
npm ci
npm run build

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache || true

php artisan optimize

if command -v supervisorctl >/dev/null 2>&1; then
  supervisorctl restart nexshape-worker:* || true
  supervisorctl restart nexshape-scheduler:* || true
fi

php artisan app:deploy:checklist --target=production || true

echo "Otimizacao concluida."
