# Backend API - Módulo de Usuario

**API REST construida con Laravel (solo APIs, sin vistas)**

Este backend es una **API REST pura** que solo proporciona endpoints JSON. No tiene vistas ni renderiza HTML. El frontend (Vue.js) consume estas APIs.

## ✅ Estado: COMPLETO Y LISTO PARA USAR

Este backend está **100% completo** y listo para mover a otro proyecto.

## 📦 Instalación

### 1. Copiar Backend a Nuevo Proyecto

Copia toda la carpeta `backend/` a tu nuevo proyecto/repositorio.

### 2. Instalar Dependencias

```bash
cd backend
composer install
```

### 3. Configurar Variables de Entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Editar `.env`

Configura las siguientes variables:

```env
APP_NAME="Módulo Usuario API"
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000

DB_CONNECTION=mongodb
MONGODB_URI=tu_uri_de_mongodb
MONGODB_DATABASE=equipo

GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

FACEBOOK_CLIENT_ID=tu_client_id
FACEBOOK_CLIENT_SECRET=tu_client_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback

CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:5173
```

### 5. Probar Backend

```bash
php artisan serve
```

El backend estará disponible en `http://localhost:8000`

## 🚀 Desarrollo

```bash
php artisan serve
```

La API estará disponible en `http://localhost:8000`

## 📋 Endpoints API

### Públicos
- `POST /api/login` - Login
- `POST /api/register` - Registro
- `GET /api/preguntas-secretas` - Obtener preguntas secretas
- `POST /api/password/verify-email` - Verificar email
- `POST /api/password/verify-answer` - Verificar respuesta secreta
- `POST /api/password/update` - Actualizar contraseña

### Protegidos (requieren token)
- `GET /api/user` - Obtener usuario actual
- `POST /api/logout` - Cerrar sesión

### OAuth (web routes)
- `GET /auth/google` - Redirect a Google
- `GET /auth/google/callback` - Callback de Google
- `GET /auth/facebook` - Redirect a Facebook
- `GET /auth/facebook/callback` - Callback de Facebook

## 🔐 Autenticación

La API usa Laravel Sanctum para autenticación con tokens. Los tokens se envían en el header:
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

## 🎯 Listo para Usar

El backend está **completo y listo** para mover a otro proyecto y empezar a usarlo inmediatamente.
