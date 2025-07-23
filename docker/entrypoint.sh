#!/bin/bash

# Salir inmediatamente si un comando falla
set -e

# Ejecutar migraciones de la base de datos
php artisan migrate --force

# Optimizar la configuración de Laravel para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar el proceso principal (Supervisor)
exec "$@"
