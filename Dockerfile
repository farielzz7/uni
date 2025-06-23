FROM php:8.2-fpm

# Dependencias básicas
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libmcrypt-dev \
    && docker-php-ext-install pdo pdo_mysql zip exif pcntl bcmath

# Instalar Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configuración de Xdebug
COPY ./docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Copia el código fuente
COPY . .

# Instala dependencias de Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permisos para almacenamiento y caché
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Comando para iniciar Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
