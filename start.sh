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

# Generar JWT_SECRET si no está configurada
echo "Verificando JWT_SECRET..."
if [ -z "$JWT_SECRET" ]; then
    echo "JWT_SECRET no está configurada, generando..."
    # Generar una clave secreta de 64 caracteres usando OpenSSL
    # Usamos base64 para asegurar caracteres seguros
    JWT_SECRET=$(openssl rand -base64 64 | tr -d '\n' | head -c 64)
    if [ -z "$JWT_SECRET" ]; then
        # Fallback: usar /dev/urandom si openssl no está disponible
        JWT_SECRET=$(head -c 64 /dev/urandom | base64 | tr -d '\n' | head -c 64)
    fi
    export JWT_SECRET
    echo "JWT_SECRET generada (longitud: ${#JWT_SECRET})"
    echo "NOTA: Esta clave se regenerara en cada reinicio si no se configura en Render"
else
    echo "JWT_SECRET ya está configurada desde variables de entorno"
fi

# Verificar que JWT_SECRET esté disponible
if [ -z "$JWT_SECRET" ]; then
    echo "ERROR: JWT_SECRET no pudo ser generada"
    exit 1
fi

# Establecer JWT_SECRET en el entorno para todos los procesos PHP
export JWT_SECRET

# Cachear configuraciones (JWT_SECRET debe estar disponible como variable de entorno)
# Pasamos JWT_SECRET explícitamente para asegurar que esté disponible
echo "Cacheando configuraciones..."
env JWT_SECRET="$JWT_SECRET" php artisan config:cache 2>&1 || echo "Config cache failed (continuando)"

# Cachear rutas
echo "Cacheando rutas..."
php artisan route:cache 2>&1 || echo "Route cache failed (continuando)"

# Verificar permisos finales
echo "Verificando permisos finales..."
ls -ld storage/framework/views || echo "ERROR: storage/framework/views no existe o no tiene permisos"

# Iniciar servidor con JWT_SECRET disponible en el entorno
echo "=== Iniciando servidor en puerto $PORT ==="
echo "JWT_SECRET disponible: ${JWT_SECRET:0:20}..."
# Asegurar que JWT_SECRET esté disponible para el servidor PHP
exec env JWT_SECRET="$JWT_SECRET" php -S 0.0.0.0:$PORT -t public

