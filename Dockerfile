FROM php:8.3-cli

# ── Extensions système nécessaires à Laravel + dompdf + qrcode ──
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        bcmath \
        gd \
        zip \
        exif \
        intl \
        dom \
        xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ── Composer ──
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ── Dépendances PHP (sans dev) ──
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# ── Code source ──
COPY . .

# ── Permissions storage ──
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ── Port exposé (Railway injecte $PORT) ──
EXPOSE 8000

# ── Démarrage : migrations + storage:link + serveur ──
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan storage:link --quiet 2>/dev/null || true && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
