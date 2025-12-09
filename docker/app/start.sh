#!/bin/bash
set -e

# Esperar a que la base de datos esté lista (opcional, pero buena práctica)
# Usando wait-for-it o similar sería mejor, pero esto es un intento básico si mysql es local
# Si la DB es externa, esto podría no ser necesario si está siempre on.


echo "Esperando a que la base de datos esté lista..."
max_tries=30
counter=0
while ! php artisan db:monitor > /dev/null 2>&1; do
    counter=$((counter+1))
    if [ $counter -gt $max_tries ]; then
        echo "Error: No se pudo conectar a la base de datos después de $max_tries intentos."
        exit 1
    fi
    echo "Esperando DB... ($counter/$max_tries)"
    sleep 2
done

echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Optimizando..."
php artisan config:cache
php artisan route:cache
php artisan view:cache


# Asegurar permisos de storage y cache (por si migraciones generaron archivos como root)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "Iniciando PHP-FPM..."
exec php-fpm -y /usr/local/etc/php-fpm.conf -R
