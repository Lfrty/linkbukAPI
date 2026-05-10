#!/usr/bin/env bash
set -e # Si algo falla, el despliegue se detiene

echo "Instalando dependencias de Composer..."
composer install --no-dev --working-dir=/var/www/html

echo "Limpiando y generando caché..."
php artisan config:cache
php artisan route:cache

echo "Ejecutando migraciones..."
php artisan migrate --force

echo "¡Despliegue completado!"