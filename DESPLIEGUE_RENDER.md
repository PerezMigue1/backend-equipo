# 🚀 Guía de Despliegue en Render.com

Esta guía te ayudará a desplegar tu backend Laravel en Render.com paso a paso.

## 📋 Requisitos Previos

1. ✅ Cuenta en [Render.com](https://render.com) (gratuita)
2. ✅ Repositorio en GitHub con el código
3. ✅ URI de conexión a MongoDB (MongoDB Atlas o servidor MongoDB)
4. ✅ Credenciales de OAuth (Google y Facebook) - Opcional

## 🎯 Opción 1: Despliegue con render.yaml (Recomendado)

### Paso 1: Conectar Repositorio en Render

1. Inicia sesión en [Render.com](https://render.com)
2. Haz clic en **"New +"** → **"Blueprint"**
3. Conecta tu repositorio de GitHub: `PerezMigue1/backend-equipo`
4. Render detectará automáticamente el archivo `render.yaml`
5. Haz clic en **"Apply"**

### Paso 2: Configurar Variables de Entorno

Render creará el servicio automáticamente, pero necesitas configurar las variables de entorno:

1. Ve a tu servicio en Render
2. Haz clic en **"Environment"** en el menú lateral
3. Agrega las siguientes variables:

#### Variables Requeridas:

```env
APP_NAME=Módulo Usuario API
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:TU_CLAVE_GENERADA_AQUI
APP_URL=https://tu-app.onrender.com

# MongoDB
DB_CONNECTION=mongodb
MONGODB_URI=mongodb+srv://usuario:password@cluster.mongodb.net/?retryWrites=true&w=majority
MONGODB_DATABASE=equipo

# Frontend (actualiza con tu URL de frontend)
FRONTEND_URL=https://tu-frontend.vercel.app

# OAuth Google (opcional)
GOOGLE_CLIENT_ID=tu_google_client_id
GOOGLE_CLIENT_SECRET=tu_google_client_secret
GOOGLE_REDIRECT_URI=https://tu-app.onrender.com/auth/google/callback

# OAuth Facebook (opcional)
FACEBOOK_CLIENT_ID=tu_facebook_client_id
FACEBOOK_CLIENT_SECRET=tu_facebook_client_secret
FACEBOOK_REDIRECT_URI=https://tu-app.onrender.com/auth/facebook/callback

# CORS
CORS_ALLOWED_ORIGINS=https://tu-frontend.vercel.app,http://localhost:3000
```

#### Generar APP_KEY:

Ejecuta localmente:
```bash
php artisan key:generate --show
```

Copia la clave generada y pégala en la variable `APP_KEY` en Render.

### Paso 3: Verificar Despliegue

1. Render iniciará el despliegue automáticamente
2. Espera a que termine el build (puede tardar 5-10 minutos la primera vez)
3. Verifica que el servicio esté "Live"
4. Prueba el endpoint de health check: `https://tu-app.onrender.com/up`

## 🐳 Opción 2: Despliegue con Dockerfile

Si prefieres usar Docker (útil si necesitas extensiones específicas de PHP):

### Paso 1: Crear Servicio Web en Render

1. Inicia sesión en Render
2. Haz clic en **"New +"** → **"Web Service"**
3. Conecta tu repositorio: `PerezMigue1/backend-equipo`
4. Configura:
   - **Name**: `backend-equipo`
   - **Environment**: `Docker`
   - **Build Command**: (dejar vacío, se usa el Dockerfile)
   - **Start Command**: (dejar vacío, se usa el CMD del Dockerfile)

### Paso 2: Configurar Variables de Entorno

Igual que en la Opción 1, agrega todas las variables de entorno necesarias.

## ⚙️ Configuración Adicional

### Health Check

Render verificará automáticamente el endpoint `/up` para asegurarse de que la aplicación está funcionando.

### Logs

Puedes ver los logs en tiempo real desde el panel de Render:
1. Ve a tu servicio
2. Haz clic en **"Logs"**
3. Verás los logs en tiempo real

### Variables de Entorno Sensibles

Las variables marcadas con `sync: false` en `render.yaml` no se sincronizan automáticamente. Debes configurarlas manualmente en el panel de Render.

## 🔧 Solución de Problemas

### Error: "MongoDB extension not found"

Si usas la Opción 1 (render.yaml), Render podría no tener la extensión de MongoDB instalada por defecto. En ese caso:

1. Usa la Opción 2 (Dockerfile) que incluye la extensión de MongoDB
2. O contacta a Render para habilitar extensiones de PHP personalizadas

### Error: "APP_KEY not set"

Asegúrate de generar una clave y agregarla en las variables de entorno:
```bash
php artisan key:generate --show
```

### Error: "Storage permissions"

El script `start.sh` intenta dar permisos al directorio storage. Si hay problemas, puedes ejecutar manualmente en Render:
```bash
chmod -R 775 storage bootstrap/cache
```

### Error: "Port not found"

Render asigna automáticamente el puerto en la variable `$PORT`. El script `start.sh` ya lo maneja correctamente.

## 📝 Notas Importantes

1. **Primer despliegue**: Puede tardar 10-15 minutos mientras Render instala las dependencias
2. **Sleep después de inactividad**: En el plan gratuito, Render pone a dormir el servicio después de 15 minutos de inactividad. El primer request después del sleep puede tardar 30-60 segundos
3. **Base de datos**: Asegúrate de que tu URI de MongoDB permita conexiones desde cualquier IP (0.0.0.0/0) o agrega la IP de Render a la whitelist
4. **CORS**: Actualiza `CORS_ALLOWED_ORIGINS` con la URL de tu frontend en producción

## 🎉 Verificación Final

Una vez desplegado, verifica:

1. ✅ Health check: `https://tu-app.onrender.com/up`
2. ✅ Endpoint de preguntas: `https://tu-app.onrender.com/api/preguntas-secretas`
3. ✅ Logs sin errores en el panel de Render
4. ✅ Variables de entorno configuradas correctamente

## 🔗 URLs Importantes

- Panel de Render: https://dashboard.render.com
- Documentación de Render: https://render.com/docs
- Documentación de Laravel: https://laravel.com/docs

## 📞 Soporte

Si tienes problemas:
1. Revisa los logs en Render
2. Verifica que todas las variables de entorno estén configuradas
3. Asegúrate de que MongoDB esté accesible desde Render
4. Consulta la documentación de Render: https://render.com/docs

