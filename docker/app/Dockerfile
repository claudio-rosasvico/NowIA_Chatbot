# ==========================================
# Etapa 1: Construcción de Assets (Node)
# ==========================================
FROM node:20-alpine as frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# ==========================================
# Etapa 2: Runtime PHP
# ==========================================
FROM php:8.3-fpm-alpine

# Dependencias de sistema necesarias
# CORRECCIÓN: Se agregó 'oniguruma-dev' necesario para mbstring
RUN apk add --no-cache \
    git curl zip unzip bash shadow \
    libpng-dev libjpeg-turbo-dev libwebp-dev libxpm-dev \
    icu-dev libzip-dev zlib-dev \
    poppler-utils \
    mysql-client \
    oniguruma-dev

# Extensiones PHP
RUN docker-php-ext-configure gd --with-jpeg --with-webp
RUN docker-php-ext-install pdo_mysql mbstring intl zip bcmath exif gd pcntl

# Redis extension
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del pcre-dev $PHPIZE_DEPS

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar archivos de proyecto
COPY . .
# Copiar assets compilados desde la etapa 1
COPY --from=frontend /app/public/build public/build

# Instalar dependencias de PHP (Producción)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Permisos
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copiar script de arranque
COPY docker/app/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Config PHP básica
RUN { \
    echo "memory_limit=512M"; \
    echo "upload_max_filesize=32M"; \
    echo "post_max_size=32M"; \
    echo "expose_php=off"; \
    echo "opcache.enable=1"; \
    echo "opcache.validate_timestamps=0"; \
    } > /usr/local/etc/php/conf.d/zz-prod-tweak.ini

ENTRYPOINT ["/usr/local/bin/start.sh"]