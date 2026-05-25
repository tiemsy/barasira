# docker/php/Dockerfile

# ============================================================
# Stage 1: vendor (composer install)
# ============================================================
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev --no-interaction --no-progress \
    --prefer-dist --no-scripts --no-autoloader

# ============================================================
# Stage 2: assets (npm install + vite build)
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
# Stage 3: runtime (php-fpm + nginx + supervisor)
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

# Copy application code first
COPY . .

# Copy artefacts from build stages
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Default .env if missing (composer scripts and artisan need this to boot Laravel)
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Now that vendor + code are in place, regenerate optimized autoload.
# --no-scripts: skip Laravel's post-autoload-dump which runs
# `php artisan package:discover` — that boots Laravel and requires runtime
# env (DB, cache, etc.) that is not available at build time. Package discovery
# happens at runtime on first artisan call instead.
RUN composer dump-autoload --no-dev --optimize-autoloader --no-scripts

# Nginx + supervisor config
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default \
    && rm -f /etc/nginx/sites-enabled/default.bak
COPY docker/php/supervisor.conf /etc/supervisor/conf.d/supervisor.conf

# Permissions for runtime writable dirs
RUN chown -R www-data:www-data /var/www/html \
    && find storage -type d -exec chmod 775 {} \; \
    && find bootstrap/cache -type d -exec chmod 775 {} \;

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
