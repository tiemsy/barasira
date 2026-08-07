# Stage 1 — Dépendances frontend
# Installe les dépendances Node.js dans une couche dédiée afin de profiter du
# cache Docker tant que package.json et package-lock.json ne changent pas.
FROM node:22-bookworm-slim AS frontend-dependencies
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

# Stage 2 — Compilation frontend
# Réutilise les dépendances du stage précédent, copie uniquement les sources
# nécessaires à Vite, puis génère les assets statiques dans public/build.
FROM frontend-dependencies AS frontend-build
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build

# Stage 3 — Socle PHP commun
# Prépare l'environnement partagé par toutes les images PHP : extensions,
# Composer, Nginx, Supervisor et fichiers de configuration des services.
FROM php:8.4-fpm-bookworm AS php-base

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp/composer \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        git \
        libicu-dev \
        libonig-dev \
        libpng-dev \
        libssl-dev \
        libxml2-dev \
        libzip-dev \
        nginx \
        pkg-config \
        supervisor \
        unzip \
        zip \
        iputils-ping \
        iproute2 \
        nano \
    && docker-php-ext-install -j"$(nproc)" \
        pdo \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        pcntl \
        pdo_mysql \
        zip \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable opcache redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/php/supervisor.conf /etc/supervisor/conf.d/supervisor.conf

RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default \
    && rm -f /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html

# Stage 4 — Dépendances PHP de production
# Installe uniquement les paquets Composer nécessaires à l'application en
# production. L'autoloader est généré plus tard, après la copie des sources.
FROM php-base AS application-dependencies

COPY composer.json composer.lock ./

RUN composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --no-progress \
        --no-scripts \
        --no-autoloader

# Stage 5 — Dépendances PHP de développement
# Installe également les dépendances de développement (tests, outils de
# diagnostic, etc.) pour construire l'image locale de développement.
FROM php-base AS development-dependencies

COPY composer.json composer.lock ./

RUN composer install \
        --no-interaction \
        --prefer-dist \
        --no-progress \
        --no-scripts \
        --no-autoloader

# Stage 6 — Sources de l'application pour la release
# Assemble les dépendances de production et le code Laravel, optimise
# l'autoloader, puis crée les répertoires et le lien de stockage nécessaires.
FROM application-dependencies AS application-source

COPY . .

RUN composer dump-autoload \
        --no-dev \
        --optimize \
        --classmap-authoritative \
    && mkdir -p \
        bootstrap/cache \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
    && ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage

# Stage 7 — Image de développement
# Assemble une application complète avec les dépendances de développement,
# les assets frontend compilés et une configuration PHP adaptée au travail local.
FROM development-dependencies AS develop

ENV APP_ENV=local \
    APP_DEBUG=true \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=1

COPY . .

RUN composer dump-autoload \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && mkdir -p \
        bootstrap/cache \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
    && ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage

COPY --from=frontend-build /app/public/build ./public/build
COPY docker/php/development.ini /usr/local/etc/php/conf.d/zz-barasira-environment.ini

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl --fail --silent http://127.0.0.1/up >/dev/null || exit 1
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

# Stage 8 — Image de release
# Produit l'image déployable : caches Laravel nettoyés, fichiers de test retirés,
# assets frontend intégrés et configuration PHP orientée journalisation.
FROM application-source AS release

ENV APP_DEBUG=false

RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && rm -rf tests phpunit.xml

COPY --from=frontend-build /app/public/build ./public/build
COPY docker/php/logging.ini /usr/local/etc/php/conf.d/zz-barasira-environment.ini

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl --fail --silent http://127.0.0.1/up >/dev/null || exit 1
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

# Stage 9 — Environnement de staging
# Hérite strictement de la release et identifie l'environnement comme staging.
FROM release AS staging
ENV APP_ENV=staging

# Stage 10 — Environnement de production
# Hérite strictement de la release et identifie l'environnement comme production.
FROM release AS production
ENV APP_ENV=production
