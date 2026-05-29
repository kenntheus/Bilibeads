#!/bin/sh
set -e

# Run deploy tasks (migrations, cache clear, etc.)
sh /var/www/html/deploy.sh

# Start nginx + php-fpm via supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
