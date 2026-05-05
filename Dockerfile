FROM php:8.2-apache

# 1. Instalar dependencias del sistema (añadida libonig-dev para mbstring)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    zip \
    unzip \
    git \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalar todas las extensiones PHP necesarias de una vez
RUN docker-php-ext-install pdo pdo_pgsql zip mbstring bcmath exif gd

# 3. Configurar Apache
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Traer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 5. OPTIMIZACIÓN: Instalar dependencias primero (Builds mucho más rápidos)
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# 6. Copiar el resto del código
COPY . .

# 7. Permisos y limpieza final de Laravel
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && php artisan optimize:clear

EXPOSE 80

CMD ["apache2-foreground"]