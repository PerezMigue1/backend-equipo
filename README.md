# Backend API - Módulo de Usuario

**API REST construida con Laravel para gestión de usuarios con autenticación JWT y OAuth**

## 📖 Descripción

Este proyecto es una **API REST pura** construida con Laravel 12 que proporciona endpoints JSON para la gestión de usuarios. Incluye autenticación mediante JWT (JSON Web Tokens), verificación OTP por email con SendGrid para activación de cuentas, OAuth con Google y Facebook, y recuperación de contraseñas mediante preguntas secretas o códigos OTP.

### Características Principales

- ✅ Autenticación con JWT
- ✅ Verificación OTP por email (SendGrid) para activación de cuenta
- ✅ OAuth con Google y Facebook
- ✅ Registro y login de usuarios
- ✅ Recuperación de contraseña con preguntas secretas o OTP por email
- ✅ Base de datos MongoDB
- ✅ CORS configurado para frontend
- ✅ API REST pura (sin vistas)

### Tecnologías Utilizadas

- **Framework**: Laravel 12
- **Base de Datos**: MongoDB
- **Autenticación**: JWT (tymon/jwt-auth)
- **OAuth**: Laravel Socialite
- **PHP**: 8.2+
- **Gestor de Paquetes**: Composer

## ✅ Estado del Proyecto

**COMPLETO Y LISTO PARA USAR** - El proyecto está completamente funcional y listo para producción.

## 📋 Requisitos y Dependencias

### Requisitos del Sistema

- **PHP**: >= 8.2
- **Composer**: >= 2.0
- **Extensiones PHP requeridas**:
  - `openssl`
  - `pdo`
  - `mbstring`
  - `tokenizer`
  - `xml`
  - `ctype`
  - `json`
  - `bcmath`
  - `mongodb` (extensión PHP para MongoDB)

### Dependencias de Composer (Producción)

```json
{
  "php": "^8.2",
  "laravel/fortify": "^1.30",
  "laravel/framework": "^12.0",
  "laravel/socialite": "^5.23",
  "mongodb/laravel-mongodb": "^5.5",
  "mongodb/mongodb": "*",
  "tymon/jwt-auth": "^2.2"
}
```

### Dependencias de Composer (Desarrollo)

```json
{
  "fakerphp/faker": "^1.23",
  "laravel/pint": "^1.18",
  "laravel/sail": "^1.41",
  "mockery/mockery": "^1.6",
  "nunomaduro/collision": "^8.6",
  "phpunit/phpunit": "^11.5.3"
}
```

### Servicios Externos Requeridos

- **MongoDB**: Base de datos (local o MongoDB Atlas)
- **SendGrid**: Para envío de emails con códigos OTP (obligatorio)
- **Google OAuth**: Para autenticación con Google (opcional)
- **Facebook OAuth**: Para autenticación con Facebook (opcional)

### Variables de Entorno Requeridas

**Obligatorias:**
- `APP_KEY` - Clave de encriptación de Laravel
- `JWT_SECRET` - Clave secreta para JWT
- `MONGODB_URI` - URI de conexión a MongoDB
- `MONGODB_DATABASE` - Nombre de la base de datos
- `SENDGRID_API_KEY` - API Key de SendGrid para envío de emails
- `SENDGRID_FROM_EMAIL` - Email remitente verificado en SendGrid
- `SENDGRID_FROM_NAME` - Nombre del remitente (opcional, por defecto: "Módulo Usuario API")

**Opcionales (para OAuth):**
- `GOOGLE_CLIENT_ID` - ID de cliente de Google OAuth
- `GOOGLE_CLIENT_SECRET` - Secreto de cliente de Google OAuth
- `GOOGLE_REDIRECT_URI` - URI de redirección de Google OAuth
- `FACEBOOK_CLIENT_ID` - ID de cliente de Facebook OAuth
- `FACEBOOK_CLIENT_SECRET` - Secreto de cliente de Facebook OAuth
- `FACEBOOK_REDIRECT_URI` - URI de redirección de Facebook OAuth

**Configuración:**
- `APP_NAME` - Nombre de la aplicación
- `APP_URL` - URL de la aplicación
- `FRONTEND_URL` - URL del frontend
- `CORS_ALLOWED_ORIGINS` - Orígenes permitidos para CORS

## 📦 Instalación

### 1. Clonar o Copiar el Repositorio

```bash
git clone <url-del-repositorio>
cd backend-equipo
```

### 2. Instalar PHP y Extensiones

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-common php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath
sudo apt install php8.2-dev  # Para compilar extensiones
sudo pecl install mongodb
sudo echo "extension=mongodb.so" | sudo tee /etc/php/8.2/cli/conf.d/20-mongodb.ini
sudo echo "extension=mongodb.so" | sudo tee /etc/php/8.2/fpm/conf.d/20-mongodb.ini
```

**macOS (con Homebrew):**
```bash
brew install php@8.2
brew install mongodb/brew/mongodb-community
pecl install mongodb
echo "extension=mongodb.so" >> $(php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||")
```

**Windows:**
- Descargar PHP 8.2 desde [php.net](https://windows.php.net/download/)
- Instalar extensión MongoDB desde [pecl.php.net](https://pecl.php.net/package/mongodb)
- O usar XAMPP/WAMP que incluye PHP

### 3. Instalar Composer

```bash
# Linux/macOS
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Windows: Descargar desde https://getcomposer.org/download/
```

### 4. Instalar Dependencias de Composer

```bash
composer install
```

### 5. Configurar Variables de Entorno

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

### 6. Configurar Permisos (Linux/macOS)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # Si usas Apache/Nginx
```

### 7. Crear Directorios Necesarios

```bash
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
```

### 8. Editar `.env`

Configura las siguientes variables:

```env
APP_NAME="Módulo Usuario API"
APP_ENV=local
APP_KEY=base64:...  # Generado automáticamente con php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000

DB_CONNECTION=mongodb
MONGODB_URI=mongodb://localhost:27017
# O para MongoDB Atlas:
# MONGODB_URI=mongodb+srv://usuario:password@cluster.mongodb.net/database
MONGODB_DATABASE=equipo

JWT_SECRET=...  # Generado automáticamente con php artisan jwt:secret

# SendGrid (Obligatorio para OTP)
SENDGRID_API_KEY=SG.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
SENDGRID_FROM_EMAIL=noreply@tudominio.com
SENDGRID_FROM_NAME="Módulo Usuario API"

# OAuth (Opcional)
GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

FACEBOOK_CLIENT_ID=tu_client_id
FACEBOOK_CLIENT_SECRET=tu_client_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback

CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:5173
```

### 9. Verificar Instalación

```bash
# Verificar que PHP tiene todas las extensiones
php -m | grep -E "mongodb|openssl|mbstring|tokenizer|xml|ctype|json|bcmath"

# Verificar que Laravel funciona
php artisan --version

# Verificar rutas
php artisan route:list
```

### 10. Iniciar el Servidor

**Desarrollo:**
```bash
php artisan serve
```

**Producción (con servidor web):**
- Configurar Apache/Nginx para apuntar al directorio `public/`
- O usar el script `start.sh` para despliegue

El backend estará disponible en `http://localhost:8000`

### 11. Verificar Health Check

```bash
curl http://localhost:8000/up
# Debe devolver: {"status":"ok","timestamp":"..."}
```

## 🚀 Desarrollo

```bash
php artisan serve
```

La API estará disponible en `http://localhost:8000`

## 🔧 Comandos Útiles

### Limpiar Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Regenerar Claves

```bash
php artisan key:generate
php artisan jwt:secret
```

### Verificar Configuración

```bash
# Ver todas las rutas
php artisan route:list

# Ver configuración de la aplicación
php artisan config:show

# Verificar conexión a MongoDB
php artisan tinker
# Luego en tinker: DB::connection('mongodb')->getMongoClient()->listDatabases();
```

## ⚠️ Solución de Problemas

### Error: "Class 'MongoDB\Client' not found"
- **Solución**: Instalar extensión MongoDB de PHP: `pecl install mongodb`

### Error: "JWT_SECRET is not set"
- **Solución**: Ejecutar `php artisan jwt:secret` o configurar manualmente en `.env`

### Error: "APP_KEY is not set"
- **Solución**: Ejecutar `php artisan key:generate`

### Error de permisos en storage/
- **Solución**: `chmod -R 775 storage bootstrap/cache`

### Error de CORS
- **Solución**: Verificar que `CORS_ALLOWED_ORIGINS` en `.env` incluya el origen del frontend

### Error: "SENDGRID_API_KEY no está configurada"
- **Solución**: Configurar `SENDGRID_API_KEY` y `SENDGRID_FROM_EMAIL` en `.env`. Obtén tu API Key desde [SendGrid](https://app.sendgrid.com/settings/api_keys)

### Error: "No se pudo enviar el correo de activación"
- **Solución**: 
  - Verificar que el email remitente (`SENDGRID_FROM_EMAIL`) esté verificado en SendGrid
  - Verificar que la API Key tenga permisos de envío de emails
  - Revisar los logs en `storage/logs/laravel.log` para más detalles

## 📋 Endpoints API

### Públicos

#### Autenticación
- `POST /api/login` - Iniciar sesión
  ```json
  {
    "email": "usuario@example.com",
    "password": "password123"
  }
  ```
  Respuesta:
  ```json
  {
    "message": "Login exitoso",
    "token": "eyJ0eXAiOiJKV1QiLCJh...",
    "user": {
      "id": "...",
      "name": "Usuario",
      "email": "usuario@example.com"
    }
  }
  ```

- `POST /api/register` - Registrar nuevo usuario (envía OTP por email)
  ```json
  {
    "name": "Usuario",
    "email": "usuario@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "pregunta_secreta": "¿Cuál es el nombre de tu primera mascota?",
    "respuesta_secreta": "Doki"
  }
  ```
  Respuesta:
  ```json
  {
    "message": "Registro exitoso. Ingresa el código enviado a tu correo para activar tu cuenta. El código expira en 10 minutos.",
    "email": "usuario@example.com"
  }
  ```

#### Verificación OTP (Activación de Cuenta)
- `POST /api/otp/verify-activation` - Verificar código OTP para activar cuenta
  ```json
  {
    "email": "usuario@example.com",
    "code": "123456"
  }
  ```
  Respuesta exitosa:
  ```json
  {
    "message": "Código verificado correctamente. Cuenta activada.",
    "token": "eyJ0eXAiOiJKV1QiLCJh...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": "...",
      "name": "Usuario",
      "email": "usuario@example.com",
      "email_verified_at": "2024-01-01 12:00:00"
    }
  }
  ```

- `POST /api/otp/resend-activation` - Reenviar código OTP de activación
  ```json
  {
    "email": "usuario@example.com"
  }
  ```

#### Recuperación de Contraseña
- `GET /api/preguntas-secretas` - Obtener lista de preguntas secretas disponibles
- `POST /api/password/verify-email` - Verificar que el email existe
  - Método pregunta secreta (por defecto): `{"email": "...", "method": "pregunta"}`
  - Método OTP: `{"email": "...", "method": "otp"}` (envía código OTP por email)
- `POST /api/password/verify-answer` - Verificar respuesta secreta
- `POST /api/password/update` - Actualizar contraseña
  - Con pregunta secreta: `{"email": "...", "new_password": "...", "new_password_confirmation": "...", "method": "pregunta", "respuesta_secreta": "..."}`
  - Con OTP: `{"email": "...", "new_password": "...", "new_password_confirmation": "...", "method": "otp", "otp_code": "123456"}`

#### OTP para Recuperación de Contraseña
- `POST /api/otp/verify-password-recovery` - Verificar código OTP para recuperación
  ```json
  {
    "email": "usuario@example.com",
    "code": "123456"
  }
  ```
- `POST /api/otp/resend-password-recovery` - Reenviar código OTP de recuperación
  ```json
  {
    "email": "usuario@example.com"
  }
  ```

### Protegidos (requieren token JWT)

**Header requerido:**
```
Authorization: Bearer {token}
```

- `GET /api/user` - Obtener información del usuario autenticado
- `POST /api/logout` - Cerrar sesión

### OAuth (web routes)

- `GET /auth/google` - Iniciar autenticación con Google
- `GET /auth/google/callback` - Callback de Google OAuth
- `GET /auth/facebook` - Iniciar autenticación con Facebook
- `GET /auth/facebook/callback` - Callback de Facebook OAuth

### Health Check

- `GET /up` - Verificar estado del servidor
  ```json
  {
    "status": "ok",
    "timestamp": "2024-01-01 12:00:00"
  }
  ```

## 🔐 Autenticación

La API usa JWT (JSON Web Tokens) para autenticación. Los tokens se envían en el header:
```
Authorization: Bearer {token}
```

## 🗄️ Base de Datos

- **MongoDB**: Base de datos `equipo`
- **Colección usuarios**: `usuario`
- **Colección preguntas secretas**: `recuperar-password`

## 📁 Estructura

```
backend/
├── app/
│   ├── Http/Controllers/Api/  # Controllers API
│   ├── Models/                # Models
│   ├── Actions/Fortify/       # Actions de Fortify
│   └── Providers/             # Service Providers
├── routes/
│   ├── api.php                # Rutas API
│   └── web.php                # Rutas OAuth
├── config/                    # Configuración
├── bootstrap/                 # Bootstrap
├── database/                  # Migraciones, seeders
├── public/                    # Punto de entrada
├── storage/                   # Almacenamiento
└── tests/                     # Tests
```

## 🚢 Despliegue

Ver archivos de configuración para Render.com:
- `Dockerfile`
- `render.yaml`
- `start.sh`

## 📚 Documentación

- `INSTALACION.md` - Instrucciones detalladas de instalación
- `CHECKLIST.md` - Checklist de verificación
- `FRONTEND_INTEGRATION.md` - **Guía completa de integración para el frontend (OTP, flujos, ejemplos)**
- Este `README.md` - Documentación principal

## ⚠️ Notas Importantes

- Este backend es **solo API** - no tiene vistas
- El frontend consume esta API
- Ambos comparten la misma base de datos MongoDB
- Las rutas OAuth están en `web.php` porque necesitan sesiones
- CORS está configurado para permitir requests del frontend

## ✅ Verificación

Para verificar que todo funciona:

1. ✅ El backend responde en `http://localhost:8000`
2. ✅ Los endpoints API responden correctamente
3. ✅ OAuth funciona (Google y Facebook)
4. ✅ CORS permite requests del frontend
5. ✅ Los tokens de autenticación funcionan

## 🎯 Uso Rápido

### Ejemplo de Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "usuario@example.com",
    "password": "password123"
  }'
```

### Ejemplo de Obtener Usuario (con token)

```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer {tu_token_jwt}"
```

## 📝 Licencia

Este proyecto está bajo la licencia MIT.

## 🤝 Contribuir

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📞 Soporte

Para reportar problemas o solicitar características, por favor abre un issue en el repositorio.

## 🎯 Listo para Usar

El backend está **completo y listo** para usar en producción o integrar con cualquier frontend.
