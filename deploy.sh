#!/bin/sh
set -e

echo "=== Bilibeads Deploy ==="

# Generate app key if not already set
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

# Run pending migrations
php artisan migrate --force

# Clear and cache config/routes for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage symlink if not already present
php artisan storage:link 2>/dev/null || true

echo "=== Deploy complete ==="
