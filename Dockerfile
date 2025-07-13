FROM php:8.2-fpm

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git zip unzip curl nginx supervisor \
    libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev libmcrypt-dev \
    && docker-php-ext-install pdo pdo_mysql zip exif pcntl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear directorio de logs para supervisor
RUN mkdir -p /var/log/supervisor

# Copiar configuración de Nginx y Supervisor
COPY ./docker/nginx/conf.d/conf.conf /etc/nginx/conf.d/default.conf
COPY ./docker/supervisord.conf /etc/supervisord.conf

# Eliminar configuración por defecto de nginx
RUN rm -f /etc/nginx/sites-enabled/default

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar el código de Laravel
COPY . .

# Instalar dependencias de Laravel (sin dev)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Configurar permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Exponer el puerto 80
EXPOSE 80

# Ejecutar Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]