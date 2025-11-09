#!/bin/bash
# NO usar set -e para continuar aunque falle algo

echo "=== Iniciando aplicacion Laravel ==="

# Render ya tiene las variables de entorno configuradas
# No necesitamos crear .env manualmente

# Mostrar variables de entorno para debugging
echo "=== Variables de entorno ==="
env | grep APP_ || echo "No APP_ variables found"

# Cachear configuraciones (esto usa las variables de entorno de Render)
echo "Cacheando configuraciones..."
php artisan config:cache || echo "Config cache failed"
php artisan route:cache || echo "Route cache failed"

# Iniciar servidor
echo "=== Iniciando servidor en puerto $PORT ==="
php -S 0.0.0.0:$PORT -t public

