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

# Clear any stale caches first
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run pending migrations
php artisan migrate --force

# Rebuild caches for production performance
php artisan config:cache
php artisan route:cache

# Create storage symlink if not already present
php artisan storage:link 2>/dev/null || true

echo "=== Deploy complete ==="
