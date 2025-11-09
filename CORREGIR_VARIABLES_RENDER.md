# 🔧 Corregir Variables de Entorno en Render

## ⚠️ Variables que Necesitan Corrección

Según la imagen proporcionada, hay dos variables que necesitan corregirse:

### 1. APP_URL ❌ → ✅

**Valor actual (incorrecto):**
```
APP_URL=backend-equipo
```

**Valor correcto:**
```
APP_URL=https://backend-equipo.onrender.com
```

### 2. GOOGLE_REDIRECT_URI ❌ → ✅

**Valor actual (incorrecto):**
```
GOOGLE_REDIRECT_URI=https://modulousuario.onrender.com/auth/google/callback
```

**Valor correcto:**
```
GOOGLE_REDIRECT_URI=https://backend-equipo.onrender.com/auth/google/callback
```

## 📋 Pasos para Corregir en Render

1. **Ve a tu servicio en Render:**
   - Inicia sesión en https://dashboard.render.com
   - Selecciona el servicio `backend-equipo`

2. **Accede a las Variables de Entorno:**
   - Haz clic en **"Environment"** en el menú lateral

3. **Corrige APP_URL:**
   - Busca la variable `APP_URL`
   - Haz clic en el icono de lápiz (editar)
   - Cambia el valor a: `https://backend-equipo.onrender.com`
   - Haz clic en **"Save"**

4. **Corrige GOOGLE_REDIRECT_URI:**
   - Busca la variable `GOOGLE_REDIRECT_URI`
   - Haz clic en el icono de lápiz (editar)
   - Cambia el valor a: `https://backend-equipo.onrender.com/auth/google/callback`
   - Haz clic en **"Save"**

5. **Reinicia el Servicio:**
   - Después de cambiar las variables, Render debería reiniciar automáticamente
   - Si no se reinicia automáticamente, haz clic en **"Manual Deploy"** → **"Deploy latest commit"**

## ✅ Verificación de Todas las Variables

Asegúrate de que todas las variables estén configuradas correctamente:

```env
APP_NAME=Módulo Usuario API
APP_ENV=production
APP_DEBUG=false
APP_KEY=CYWjK9HEBXqSlINaeTsmiwMr9Vxuc2X1wXK641Bk7ao=  # (o la que Render generó)
APP_URL=https://backend-equipo.onrender.com  # ✅ CORREGIR

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=mongodb
MONGODB_URI=mongodb+srv://usuario:password@cluster.mongodb.net/database?retryWrites=true&w=majority
MONGODB_DATABASE=equipo

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

FRONTEND_URL=https://modulo-usuario.netlify.app

GOOGLE_CLIENT_ID=tu_google_client_id
GOOGLE_CLIENT_SECRET=tu_google_client_secret
GOOGLE_REDIRECT_URI=https://backend-equipo.onrender.com/auth/google/callback  # ✅ CORREGIR

CORS_ALLOWED_ORIGINS=https://modulo-usuario.netlify.app,http://localhost:3000
```

## 🔐 Actualizar Google OAuth

Después de corregir el `GOOGLE_REDIRECT_URI`, también necesitas actualizarlo en Google Cloud Console:

1. **Ve a Google Cloud Console:**
   - https://console.cloud.google.com
   - Navega a **APIs & Services** → **Credentials**

2. **Edita tu OAuth 2.0 Client ID:**
   - Busca tu OAuth Client ID en Google Cloud Console
   - Haz clic en **Edit**

3. **Actualiza Authorized redirect URIs:**
   - Asegúrate de que tenga: `https://backend-equipo.onrender.com/auth/google/callback`
   - Elimina el URI incorrecto si existe: `https://modulousuario.onrender.com/auth/google/callback`
   - Haz clic en **Save**

## 🧪 Verificar que Funciona

Después de hacer los cambios:

1. **Verifica APP_URL:**
   ```bash
   curl https://backend-equipo.onrender.com/up
   ```
   Debe responder con: `{"status":"ok"}`

2. **Verifica OAuth de Google:**
   - Visita: `https://backend-equipo.onrender.com/auth/google`
   - Debe redirigirte a Google para autenticación
   - Después de autenticarte, debe redirigirte de vuelta a tu frontend

3. **Verifica CORS:**
   - Desde tu frontend en `https://modulo-usuario.netlify.app`
   - Intenta hacer una petición al backend
   - No debería haber errores de CORS

## 📝 Notas Importantes

- **APP_URL**: Debe ser la URL completa del backend (con `https://`)
- **GOOGLE_REDIRECT_URI**: Debe apuntar a tu backend, no a otro servicio
- **FRONTEND_URL**: Ya está correcto (`https://modulo-usuario.netlify.app`)
- **CORS_ALLOWED_ORIGINS**: Ya incluye tu frontend correctamente

## 🚨 Problemas Comunes

### Si APP_URL no se actualiza:
- Verifica que hayas guardado los cambios
- Reinicia el servicio manualmente
- Revisa los logs para ver si hay errores

### Si OAuth no funciona:
- Verifica que el `GOOGLE_REDIRECT_URI` esté actualizado en Google Cloud Console
- Verifica que el URI en Render coincida exactamente con el de Google
- Revisa los logs del backend para ver errores de OAuth

### Si hay errores de CORS:
- Verifica que `CORS_ALLOWED_ORIGINS` incluya tu frontend
- Verifica que `FRONTEND_URL` esté correcto
- Reinicia el servicio después de cambiar las variables

