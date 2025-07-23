FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    nginx supervisor \
    libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev unzip curl \
    && docker-php-ext-install pdo pdo_mysql zip exif pcntl bcmath \
    && apt-get clean && rm -rf /var/lib/apt-lists/*

# Instalar Composer desde imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Redirigir logs de Nginx y PHP-FPM a stderr para CloudWatch
RUN ln -sf /dev/stderr /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Crear directorio de logs para Supervisor
RUN mkdir -p /var/log/supervisor

# Copiar configuración de Nginx y Supervisor
COPY ./docker/nginx/conf.d/conf.conf /etc/nginx/conf.d/default.conf
COPY ./docker/supervisord.conf /etc/supervisord.conf

# Eliminar configuración por defecto de Nginx
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
