#!/bin/bash
set -e

# Esperar a que la base de datos esté lista (opcional, pero buena práctica)
# Usando wait-for-it o similar sería mejor, pero esto es un intento básico si mysql es local
# Si la DB es externa, esto podría no ser necesario si está siempre on.

echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Optimizando..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Iniciando PHP-FPM..."
exec php-fpm -y /usr/local/etc/php-fpm.conf -R
