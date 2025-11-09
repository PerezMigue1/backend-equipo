#!/bin/bash
# NO usar set -e para continuar aunque falle algo

echo "=== Iniciando aplicacion Laravel ==="

# Crear directorios necesarios si no existen (CRÍTICO)
echo "Creando directorios de storage y cache..."
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Establecer permisos para storage y bootstrap/cache (777 para Docker)
echo "Estableciendo permisos..."
chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# Verificar que los directorios existen
echo "Verificando directorios..."
ls -la storage/framework/ || echo "Warning: No se pudo listar storage/framework"
ls -la storage/framework/views/ || echo "Warning: No se pudo listar storage/framework/views"
ls -la bootstrap/cache/ || echo "Warning: No se pudo listar bootstrap/cache"

# Limpiar cache anterior solo si los directorios existen
if [ -d storage/framework/cache/data ]; then
    echo "Limpiando cache anterior..."
    php artisan cache:clear 2>/dev/null || echo "Cache clear failed (continuando)"
fi

if [ -d bootstrap/cache ]; then
    php artisan config:clear 2>/dev/null || echo "Config clear failed (continuando)"
    php artisan route:clear 2>/dev/null || echo "Route clear failed (continuando)"
fi

if [ -d storage/framework/views ]; then
    php artisan view:clear 2>/dev/null || echo "View clear failed (continuando)"
fi

# Asegurar que los directorios existen después de limpiar
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache
chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# Cachear configuraciones
echo "Cacheando configuraciones..."
php artisan config:cache 2>&1 || echo "Config cache failed (continuando)"

# Cachear rutas
echo "Cacheando rutas..."
php artisan route:cache 2>&1 || echo "Route cache failed (continuando)"

# Verificar permisos finales
echo "Verificando permisos finales..."
ls -ld storage/framework/views || echo "ERROR: storage/framework/views no existe o no tiene permisos"

# Iniciar servidor
echo "=== Iniciando servidor en puerto $PORT ==="
php -S 0.0.0.0:$PORT -t public

