# Usa una imagen oficial de PHP como base
FROM php:8.2-cli

# Instala las extensiones necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Instala extensiones de PHP
RUN docker-php-ext-install pdo pdo_mysql

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia el código de la aplicación
WORKDIR /var/www
COPY . .

# Instala dependencias de Composer
RUN composer install

# Permisos para el directorio de almacenamiento
RUN chown -R www-data:www-data /var/www/storage

# Comando para iniciar el servidor PHP de Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
