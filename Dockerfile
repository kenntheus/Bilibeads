FROM php:8.2-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    nginx \
    nodejs \
    npm \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libzip-dev \
    zip \
    unzip \
    mysql-client

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql mbstring gd zip bcmath opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# PHP dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Node dependencies & build assets
COPY package.json package-lock.json vite.config.js ./
COPY resources resources/
RUN npm ci && npm run build

# Copy app source
COPY . .

# Finish composer scripts now that full source is present
RUN composer run-script post-autoload-dump --no-interaction || true

# Ensure all required storage directories exist
RUN mkdir -p storage/framework/views \
             storage/framework/sessions \
             storage/framework/cache/data \
             storage/app/public \
             storage/logs \
             bootstrap/cache

# Storage & cache permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Supervisor config (runs nginx + php-fpm together)
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
