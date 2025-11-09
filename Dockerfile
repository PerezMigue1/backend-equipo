# Dockerfile para Laravel en Render

FROM php:8.2-cli

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libssl-dev \
    openssl \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar extensión MongoDB
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos (excluyendo node_modules y otros archivos innecesarios)
COPY . /var/www/html

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Crear directorios necesarios de storage y cache (asegurar que existan)
RUN mkdir -p storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Establecer permisos para storage y bootstrap/cache (777 para asegurar escritura)
RUN chmod -R 777 storage bootstrap/cache

# Exponer puerto
EXPOSE 8000

# Hacer ejecutable el script de inicio
RUN chmod +x /var/www/html/start.sh

# Usar el script de inicio
CMD ["/var/www/html/start.sh"]
