# docker/php/Dockerfile

# ============================================================
# Stage 1: assets (npm install + vite build)
# ============================================================
FROM node:20 AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# ============================================================
# Stage 2: runtime (php-fpm + nginx + supervisor + composer install)
# ============================================================
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip nginx supervisor \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    libicu-dev libssl-dev pkg-config \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath intl gd zip \
    && docker-php-ext-enable opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy full application code (incl. composer.json, composer.lock, app/, etc.)

COPY . .

# Copy artefacts from build stages
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Now that vendor + code are in place, regenerate optimized autoload

RUN composer dump-autoload

# Default .env if missing (Coolify overrides at runtime via env vars)
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Clear stale caches (will be regenerated at runtime by config:cache etc.)
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# Install PHP dependencies with full code present so any autoload classmap
# can resolve. --no-scripts skips Laravel's package:discover (it needs runtime
# env not available at build time); it runs lazily on first artisan call.
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
        --no-dev --no-interaction --no-progress \
        --prefer-dist --no-scripts --optimize-autoloader

# Copy built front-end assets from the node stage
COPY --from=assets /app/public/build ./public/build

# Nginx + supervisor config
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default \
    && rm -f /etc/nginx/sites-enabled/default.bak
COPY docker/php/supervisor.conf /etc/supervisor/conf.d/supervisor.conf

# Permissions for runtime writable dirs (php-fpm pool runs as www-data)
RUN chown -R www-data:www-data /var/www/html \
    && find storage -type d -exec chmod 775 {} \; \
    && find bootstrap/cache -type d -exec chmod 775 {} \;

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
