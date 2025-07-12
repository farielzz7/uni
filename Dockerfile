FROM php:8.2-fpm

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    curl \
    nginx \
    supervisor \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libmcrypt-dev \
    && docker-php-ext-install pdo pdo_mysql zip exif pcntl bcmath \
    #&& pecl install xdebug && docker-php-ext-enable xdebug \
    && rm -rf /var/lib/apt/lists/*

# Configuración de Xdebug
# COPY ./docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear directorio de logs para supervisor
RUN mkdir -p /var/log/supervisor

# Copiar configuración de Nginx
COPY ./docker/nginx/conf.d/conf.conf /etc/nginx/conf.d/default.conf

# Eliminar configuración por defecto de nginx
RUN rm -f /etc/nginx/sites-enabled/default

# Copiar configuración de Supervisor
COPY ./docker/supervisord.conf /etc/supervisord.conf

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar el código de Laravel
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Configurar permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Crear el archivo .env si no existe (para evitar errores)
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Generar la key de Laravel
RUN php artisan key:generate --force

# Exponer el puerto 80
EXPOSE 80

# Ejecutar Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]