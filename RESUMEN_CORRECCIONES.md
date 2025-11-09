# 🔧 Resumen de Correcciones Necesarias en Render

## ❌ Variables que Necesitan Corrección Inmediata

### 1. APP_URL
**Problema:** Actualmente está configurado como `backend-equipo` (solo el nombre)
**Solución:** Debe ser `https://backend-equipo.onrender.com`

### 2. GOOGLE_REDIRECT_URI  
**Problema:** Actualmente apunta a `https://modulousuario.onrender.com/auth/google/callback`
**Solución:** Debe ser `https://backend-equipo.onrender.com/auth/google/callback`

## 🚀 Pasos Rápidos para Corregir

1. **Ve a Render Dashboard:**
   - https://dashboard.render.com
   - Selecciona el servicio `backend-equipo`

2. **Edita las Variables de Entorno:**
   - Haz clic en **Environment** en el menú lateral
   - Busca `APP_URL` y cámbialo a: `https://backend-equipo.onrender.com`
   - Busca `GOOGLE_REDIRECT_URI` y cámbialo a: `https://backend-equipo.onrender.com/auth/google/callback`
   - Guarda los cambios

3. **Actualiza Google OAuth:**
   - Ve a Google Cloud Console: https://console.cloud.google.com
   - APIs & Services → Credentials
   - Edita tu OAuth Client ID
   - Actualiza el Authorized redirect URI a: `https://backend-equipo.onrender.com/auth/google/callback`
   - Guarda los cambios

4. **Reinicia el Servicio:**
   - Render debería reiniciar automáticamente
   - Si no, haz un Manual Deploy

## ✅ Variables Correctas (Verificar)

- ✅ `FRONTEND_URL`: `https://modulo-usuario.netlify.app` (Correcto)
- ✅ `CORS_ALLOWED_ORIGINS`: `https://modulo-usuario.netlify.app,http://localhost:3000` (Correcto)
- ✅ `MONGODB_URI`: Configurado correctamente
- ✅ `GOOGLE_CLIENT_ID`: Configurado correctamente
- ✅ `GOOGLE_CLIENT_SECRET`: Configurado correctamente
- ❌ `APP_URL`: Necesita corrección
- ❌ `GOOGLE_REDIRECT_URI`: Necesita corrección

## 📝 Nota sobre render.yaml

El archivo `render.yaml` ha sido actualizado para que `APP_URL` se configure manualmente (con `sync: false`). Esto te permite tener más control sobre la URL y evitar problemas con la generación automática.

## 🧪 Verificación

Después de hacer los cambios, verifica:

1. Health check: `https://backend-equipo.onrender.com/up`
2. OAuth Google: `https://backend-equipo.onrender.com/auth/google`
3. API endpoint: `https://backend-equipo.onrender.com/api/preguntas-secretas`

