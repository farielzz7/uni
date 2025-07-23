FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    nginx supervisor \
    libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev unzip curl \
    && docker-php-ext-install pdo pdo_mysql zip exif pcntl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer desde imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Remover la configuración por defecto de nginx
RUN rm /etc/nginx/sites-enabled/default \
    && rm /etc/nginx/sites-available/default

# Redirigir logs
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Crear directorio para Supervisor
RUN mkdir -p /var/log/supervisor

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar el código de Laravel
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# CRÍTICO: Crear TODA la estructura de directorios de storage
RUN mkdir -p storage/framework/{cache,sessions,views} \
    && mkdir -p storage/logs \
    && mkdir -p storage/app/public \
    && mkdir -p bootstrap/cache

# Copiar configuraciones
COPY ./docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY ./docker/supervisord.conf /etc/supervisord.conf

# Establecer permisos COMPLETOS para storage
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Generar key de Laravel si no existe
RUN php artisan key:generate --no-interaction || true

# Limpiar caches
RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan view:clear

# Exponer el puerto para nginx
EXPOSE 80

# Comando de inicio
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]