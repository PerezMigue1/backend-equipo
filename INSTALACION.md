# Instalación del Backend

## 📦 Pasos de Instalación

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

Abre `.env` y configura las siguientes variables:

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

### 6. Probar Endpoints

- `GET http://localhost:8000/api/preguntas-secretas` - Debe devolver las preguntas secretas
- `POST http://localhost:8000/api/login` - Login de usuario
- `POST http://localhost:8000/api/register` - Registro de usuario

## 🚀 Despliegue en Render.com

1. Conecta tu repositorio en Render
2. Configura las variables de entorno en Render
3. Render detectará automáticamente el `Dockerfile` y `render.yaml`
4. El backend se desplegará automáticamente

## 📝 Notas

- El backend comparte la misma base de datos MongoDB que el frontend
- Las rutas OAuth están en `web.php` porque necesitan sesiones
- Los tokens se generan con Laravel Sanctum
- CORS está configurado para permitir requests del frontend

## 🔍 Verificación

Para verificar que todo funciona:

1. El backend responde en `http://localhost:8000`
2. Los endpoints API responden correctamente
3. OAuth funciona (Google y Facebook)
4. CORS permite requests del frontend

