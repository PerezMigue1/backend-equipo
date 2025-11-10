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
    # Generar APP_KEY manualmente (más confiable que artisan en este contexto)
    # Laravel requiere formato base64: seguido de 44 caracteres base64
    APP_KEY_RAW=$(openssl rand -base64 32 2>/dev/null | tr -d '\n' | head -c 44)
    if [ -z "$APP_KEY_RAW" ] || [ ${#APP_KEY_RAW} -lt 32 ]; then
        # Fallback 1: usar /dev/urandom
        APP_KEY_RAW=$(head -c 32 /dev/urandom 2>/dev/null | base64 2>/dev/null | tr -d '\n' | head -c 44)
    fi
    if [ -z "$APP_KEY_RAW" ] || [ ${#APP_KEY_RAW} -lt 32 ]; then
        # Fallback 2: usar PHP
        APP_KEY_RAW=$(php -r "echo base64_encode(random_bytes(32));" 2>/dev/null | head -c 44)
    fi
    # Limpiar saltos de línea y espacios
    APP_KEY_RAW=$(echo "$APP_KEY_RAW" | tr -d '\n' | tr -d '\r' | tr -d ' ' | head -c 44)
    APP_KEY="base64:${APP_KEY_RAW}"
    export APP_KEY
    echo "APP_KEY generada: ${APP_KEY:0:20}..."
else
    # Verificar que APP_KEY tenga el formato correcto
    APP_KEY_CLEAN=$(echo "$APP_KEY" | tr -d '\n' | tr -d '\r' | tr -d ' ')
    if [[ ! "$APP_KEY_CLEAN" =~ ^base64: ]]; then
        echo "APP_KEY no tiene el formato correcto, corrigiendo..."
        # Si APP_KEY no tiene el prefijo base64:, agregarlo
        # Tomar solo los primeros 44 caracteres después del prefijo
        if [ ${#APP_KEY_CLEAN} -ge 32 ]; then
            # Si es muy larga, tomar solo los primeros 44 caracteres
            KEY_VALUE=$(echo "$APP_KEY_CLEAN" | head -c 44)
            APP_KEY="base64:${KEY_VALUE}"
        else
            # Si es muy corta, generar una nueva
            echo "APP_KEY tiene longitud incorrecta, generando nueva..."
            APP_KEY_RAW=$(openssl rand -base64 32 2>/dev/null | tr -d '\n' | head -c 44)
            if [ -z "$APP_KEY_RAW" ] || [ ${#APP_KEY_RAW} -lt 32 ]; then
                APP_KEY_RAW=$(head -c 32 /dev/urandom 2>/dev/null | base64 2>/dev/null | tr -d '\n' | head -c 44)
            fi
            if [ -z "$APP_KEY_RAW" ] || [ ${#APP_KEY_RAW} -lt 32 ]; then
                APP_KEY_RAW=$(php -r "echo base64_encode(random_bytes(32));" 2>/dev/null | head -c 44)
            fi
            APP_KEY_RAW=$(echo "$APP_KEY_RAW" | tr -d '\n' | tr -d '\r' | tr -d ' ' | head -c 44)
            APP_KEY="base64:${APP_KEY_RAW}"
        fi
        export APP_KEY
        echo "APP_KEY corregida: ${APP_KEY:0:20}..."
    else
        # Limpiar APP_KEY de saltos de línea
        APP_KEY=$(echo "$APP_KEY" | tr -d '\n' | tr -d '\r' | tr -d ' ')
        export APP_KEY
    fi
    echo "APP_KEY configurada correctamente"
fi

# Verificar que APP_KEY esté disponible y tenga formato correcto
if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY no pudo ser generada"
    echo "Intentando generar una última vez..."
    APP_KEY_RAW=$(php -r "echo base64_encode(random_bytes(32));" 2>/dev/null | head -c 44)
    if [ -z "$APP_KEY_RAW" ]; then
        # Último recurso: usar un valor por defecto (no ideal pero funcional)
        APP_KEY="base64:$(echo 'default_key_replace_in_render' | base64 | head -c 44)"
    else
        APP_KEY="base64:${APP_KEY_RAW}"
    fi
    export APP_KEY
fi

# Verificar longitud mínima (debe tener al menos base64: + 32 caracteres = 39 caracteres mínimo)
if [ ${#APP_KEY} -lt 39 ]; then
    echo "WARNING: APP_KEY tiene longitud muy corta (${#APP_KEY}), regenerando..."
    APP_KEY_RAW=$(php -r "echo base64_encode(random_bytes(32));" 2>/dev/null | head -c 44)
    APP_KEY="base64:${APP_KEY_RAW}"
    export APP_KEY
fi

echo "APP_KEY final: ${APP_KEY:0:30}... (longitud: ${#APP_KEY})"

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
    JWT_SECRET=$(openssl rand -base64 64 2>/dev/null | tr -d '\n' | head -c 64)
    if [ -z "$JWT_SECRET" ] || [ ${#JWT_SECRET} -lt 32 ]; then
        # Fallback 1: usar /dev/urandom con base64
        JWT_SECRET=$(head -c 48 /dev/urandom 2>/dev/null | base64 2>/dev/null | tr -d '\n' | head -c 64)
    fi
    if [ -z "$JWT_SECRET" ] || [ ${#JWT_SECRET} -lt 32 ]; then
        # Fallback 2: usar PHP para generar clave hexadecimal
        JWT_SECRET=$(php -r "echo bin2hex(random_bytes(32));" 2>/dev/null)
    fi
    if [ -z "$JWT_SECRET" ] || [ ${#JWT_SECRET} -lt 32 ]; then
        # Fallback 3: usar un valor por defecto (no recomendado pero funcional)
        JWT_SECRET="default_jwt_secret_key_$(date +%s)_$(openssl rand -hex 16 2>/dev/null || echo 'fallback')"
        JWT_SECRET=$(echo "$JWT_SECRET" | head -c 64)
    fi
    export JWT_SECRET
    echo "JWT_SECRET temporal generada (longitud: ${#JWT_SECRET})"
    echo "NOTA: Configura JWT_SECRET en Render para produccion"
    echo "========================================"
else
    echo "JWT_SECRET configurada correctamente desde variables de entorno"
fi

# Verificar que JWT_SECRET esté disponible
if [ -z "$JWT_SECRET" ]; then
    echo "ERROR: JWT_SECRET no pudo ser generada, intentando una última vez..."
    JWT_SECRET=$(php -r "echo bin2hex(random_bytes(32));" 2>/dev/null)
    if [ -z "$JWT_SECRET" ]; then
        # Último recurso: generar con timestamp
        JWT_SECRET="fallback_jwt_$(date +%s)_$(php -r 'echo substr(md5(uniqid()), 0, 32);' 2>/dev/null || echo 'default')"
        JWT_SECRET=$(echo "$JWT_SECRET" | head -c 64)
    fi
    export JWT_SECRET
    echo "JWT_SECRET generada como último recurso"
fi

echo "JWT_SECRET final: ${JWT_SECRET:0:20}... (longitud: ${#JWT_SECRET})"

# Establecer APP_KEY y JWT_SECRET en el entorno para todos los procesos PHP
export APP_KEY
export JWT_SECRET

# Verificar que PORT esté definido (Render lo proporciona automáticamente)
if [ -z "$PORT" ]; then
    echo "WARNING: PORT no está definido, usando 8000 por defecto"
    export PORT=8000
fi

# Limpiar cache antes de cachear (evitar problemas con cache corrupto)
echo "Limpiando cache antes de iniciar..."
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

# Verificar que Laravel puede iniciar (test rápido)
echo "Verificando que Laravel puede iniciar..."
php artisan --version 2>&1 || echo "WARNING: Laravel no responde correctamente"

# Cachear configuraciones (APP_KEY y JWT_SECRET deben estar disponibles como variables de entorno)
# Pasamos APP_KEY y JWT_SECRET explícitamente para asegurar que estén disponibles
echo "Cacheando configuraciones..."
env APP_KEY="$APP_KEY" JWT_SECRET="$JWT_SECRET" php artisan config:cache 2>&1 || {
    echo "ERROR: Fallo al cachear configuraciones, continuando sin cache..."
    php artisan config:clear 2>/dev/null || true
}

# NO cachear rutas (puede interferir con health check /up)
# Laravel maneja automáticamente la ruta /up desde bootstrap/app.php
# Además, tenemos una ruta explícita en web.php como respaldo
echo "Omitiendo cache de rutas para evitar problemas con health check..."
# php artisan route:cache 2>&1 || {
#     echo "WARNING: Fallo al cachear rutas, continuando sin cache..."
#     php artisan route:clear 2>/dev/null || true
# }

# Verificar que la ruta /up esté disponible (health check)
echo "Verificando ruta de health check..."
php artisan route:list | grep -q "/up" && echo "✅ Ruta /up encontrada" || echo "⚠️  Ruta /up no encontrada (puede estar bien si Laravel la maneja automáticamente)"

# Verificar permisos finales
echo "Verificando permisos finales..."
ls -ld storage/framework/views 2>/dev/null || echo "WARNING: storage/framework/views no existe"

# Verificar que el directorio public existe
if [ ! -d "public" ]; then
    echo "ERROR: Directorio public no existe"
    exit 1
fi

# Iniciar servidor con APP_KEY y JWT_SECRET disponibles en el entorno
echo "=== Iniciando servidor en puerto $PORT ==="
echo "APP_KEY disponible: ${APP_KEY:0:20}... (longitud: ${#APP_KEY})"
echo "JWT_SECRET disponible: ${JWT_SECRET:0:20}... (longitud: ${#JWT_SECRET})"
echo "Directorio public: $(pwd)/public"
echo "URL de health check: http://0.0.0.0:$PORT/up"

# Asegurar que APP_KEY y JWT_SECRET estén disponibles para el servidor PHP
# Usar exec para reemplazar el proceso actual y permitir que Render gestione el proceso
exec env APP_KEY="$APP_KEY" JWT_SECRET="$JWT_SECRET" PORT="$PORT" php -S 0.0.0.0:$PORT -t public

