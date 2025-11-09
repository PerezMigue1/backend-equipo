#!/bin/bash
set -e

echo "=== Iniciando aplicacion Laravel ==="

# Verificar que el directorio de storage tenga permisos
chmod -R 775 storage bootstrap/cache || true

# Cachear configuraciones si no están cacheadas
if [ ! -f bootstrap/cache/config.php ]; then
    echo "Cacheando configuraciones..."
    php artisan config:cache || echo "Config cache failed"
fi

if [ ! -f bootstrap/cache/routes-v7.php ]; then
    echo "Cacheando rutas..."
    php artisan route:cache || echo "Route cache failed"
fi

# Iniciar servidor
echo "=== Iniciando servidor en puerto $PORT ==="
php -S 0.0.0.0:$PORT -t public

