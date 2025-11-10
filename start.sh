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

# Verificar y generar APP_KEY si no está configurada
echo "Verificando APP_KEY..."
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY no está configurada, generando..."
    # Generar APP_KEY usando artisan key:generate
    # Esto genera la clave en el formato correcto (base64:...)
    php artisan key:generate --force 2>&1 || echo "Warning: No se pudo generar APP_KEY con artisan"
    # Leer APP_KEY del archivo .env si se generó
    if [ -f .env ]; then
        APP_KEY=$(grep "^APP_KEY=" .env | cut -d '=' -f2- | tr -d '\n' | tr -d '\r')
        export APP_KEY
        echo "APP_KEY generada desde .env"
    else
        # Generar manualmente si no se pudo usar artisan
        echo "Generando APP_KEY manualmente..."
        APP_KEY="base64:$(openssl rand -base64 32 | tr -d '\n')"
        export APP_KEY
        echo "APP_KEY generada manualmente"
    fi
else
    # Verificar que APP_KEY tenga el formato correcto
    if [[ ! "$APP_KEY" =~ ^base64: ]]; then
        echo "APP_KEY no tiene el formato correcto, corrigiendo..."
        # Si APP_KEY no tiene el prefijo base64:, agregarlo
        if [ ${#APP_KEY} -ge 32 ]; then
            APP_KEY="base64:$(echo "$APP_KEY" | head -c 44)"
        else
            echo "ERROR: APP_KEY tiene longitud incorrecta"
            exit 1
        fi
        export APP_KEY
    fi
    echo "APP_KEY configurada correctamente"
fi

# Verificar que APP_KEY esté disponible
if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY no pudo ser generada"
    exit 1
fi

# Verificar JWT_SECRET
echo "Verificando JWT_SECRET..."
if [ -z "$JWT_SECRET" ]; then
    echo "========================================"
    echo "ADVERTENCIA: JWT_SECRET no está configurada"
    echo "========================================"
    echo "Por favor, configura JWT_SECRET en Render:"
    echo "  1. Ve al dashboard de Render"
    echo "  2. Selecciona el servicio 'backend-equipo'"
    echo "  3. Ve a 'Environment'"
    echo "  4. Agrega JWT_SECRET con una clave segura"
    echo ""
    echo "Generando clave temporal (se regenerara en cada reinicio)..."
    # Generar una clave secreta de 64 caracteres usando OpenSSL
    # Usamos base64 para asegurar caracteres seguros
    JWT_SECRET=$(openssl rand -base64 64 | tr -d '\n' | head -c 64 2>/dev/null || head -c 64 /dev/urandom | base64 | tr -d '\n' | head -c 64)
    export JWT_SECRET
    echo "JWT_SECRET temporal generada (longitud: ${#JWT_SECRET})"
    echo "NOTA: Configura JWT_SECRET en Render para produccion"
    echo "========================================"
else
    echo "JWT_SECRET configurada correctamente desde variables de entorno"
fi

# Verificar que JWT_SECRET esté disponible
if [ -z "$JWT_SECRET" ]; then
    echo "ERROR: JWT_SECRET no pudo ser generada"
    exit 1
fi

# Establecer APP_KEY y JWT_SECRET en el entorno para todos los procesos PHP
export APP_KEY
export JWT_SECRET

# Cachear configuraciones (APP_KEY y JWT_SECRET deben estar disponibles como variables de entorno)
# Pasamos APP_KEY y JWT_SECRET explícitamente para asegurar que estén disponibles
echo "Cacheando configuraciones..."
env APP_KEY="$APP_KEY" JWT_SECRET="$JWT_SECRET" php artisan config:cache 2>&1 || echo "Config cache failed (continuando)"

# Cachear rutas
echo "Cacheando rutas..."
php artisan route:cache 2>&1 || echo "Route cache failed (continuando)"

# Verificar permisos finales
echo "Verificando permisos finales..."
ls -ld storage/framework/views || echo "ERROR: storage/framework/views no existe o no tiene permisos"

# Iniciar servidor con APP_KEY y JWT_SECRET disponibles en el entorno
echo "=== Iniciando servidor en puerto $PORT ==="
echo "APP_KEY disponible: ${APP_KEY:0:20}..."
echo "JWT_SECRET disponible: ${JWT_SECRET:0:20}..."
# Asegurar que APP_KEY y JWT_SECRET estén disponibles para el servidor PHP
exec env APP_KEY="$APP_KEY" JWT_SECRET="$JWT_SECRET" php -S 0.0.0.0:$PORT -t public

