#!/bin/sh
set -e

echo "=== Bilibeads Deploy ==="

# Fly.io injects DATABASE_URL for attached Postgres.
# Parse it into Laravel's individual DB_ env vars when present.
if [ -n "$DATABASE_URL" ]; then
  export DB_CONNECTION=pgsql
  export DB_HOST=$(echo "$DATABASE_URL" | awk -F'[@:/]' '{print $6}')
  export DB_PORT=$(echo "$DATABASE_URL" | awk -F'[@:/]' '{print $7}')
  export DB_DATABASE=$(echo "$DATABASE_URL" | awk -F'/' '{print $NF}')
  export DB_USERNAME=$(echo "$DATABASE_URL" | awk -F'[/:@]' '{print $4}')
  export DB_PASSWORD=$(echo "$DATABASE_URL" | awk -F'[/:@]' '{print $5}')
fi

# Generate app key if not already set
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

# Run pending migrations
php artisan migrate --force

# Clear and warm config/route/view caches for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage symlink if not already present
php artisan storage:link 2>/dev/null || true

echo "=== Deploy complete ==="
