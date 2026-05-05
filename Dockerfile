# 1. Usamos PHP 8.2 (ajustado a tu nuevo composer.json)
FROM php:8.2-apache

# 2. Instalar dependencias del sistema y extensiones (zip es vital para composer)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql zip

# 3. Habilitar mod_rewrite de Apache para las rutas de Laravel
RUN a2enmod rewrite

# 4. Configurar el DocumentRoot hacia /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Traer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Copiar el proyecto
WORKDIR /var/www/html
COPY . .

# 7. Instalar dependencias sin ejecutar scripts (evita fallos de BD durante el build)
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# 8. Asegurar directorios de caché y permisos
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]