# 🚀 Pasos Rápidos para Desplegar en Render

## Paso 1: Preparar el Repositorio

1. **Asegúrate de que todos los cambios estén en GitHub:**
   ```bash
   git add .
   git commit -m "Preparar para despliegue en Render"
   git push origin main
   ```

## Paso 2: Crear Cuenta en Render

1. Ve a [https://render.com](https://render.com)
2. Crea una cuenta (puedes usar GitHub para iniciar sesión)
3. Confirma tu email

## Paso 3: Crear Nuevo Servicio

1. En el dashboard de Render, haz clic en **"New +"**
2. Selecciona **"Blueprint"**
3. Conecta tu repositorio de GitHub
4. Selecciona el repositorio: `PerezMigue1/backend-equipo`
5. Render detectará automáticamente el archivo `render.yaml`
6. Haz clic en **"Apply"**

## Paso 4: Configurar Variables de Entorno

Una vez que Render cree el servicio, necesitas configurar las variables de entorno:

1. Ve a tu servicio en Render
2. Haz clic en **"Environment"** en el menú lateral
3. Agrega las siguientes variables (las marcadas con `sync: false` deben agregarse manualmente):

### Variables Críticas:

```env
MONGODB_URI=mongodb+srv://usuario:password@cluster.mongodb.net/?retryWrites=true&w=majority
MONGODB_DATABASE=equipo
FRONTEND_URL=https://tu-frontend.vercel.app
GOOGLE_CLIENT_ID=tu_google_client_id
GOOGLE_CLIENT_SECRET=tu_google_client_secret
FACEBOOK_CLIENT_ID=tu_facebook_client_id
FACEBOOK_CLIENT_SECRET=tu_facebook_client_secret
CORS_ALLOWED_ORIGINS=https://tu-frontend.vercel.app,http://localhost:3000
```

### Generar APP_KEY:

Si Render no genera automáticamente la APP_KEY, ejecuta localmente:
```bash
php artisan key:generate --show
```
Y agrega el resultado en la variable `APP_KEY` en Render.

## Paso 5: Esperar el Despliegue

1. Render iniciará el build automáticamente
2. Puedes ver el progreso en la pestaña **"Logs"**
3. El primer despliegue puede tardar 10-15 minutos
4. Una vez completado, tu app estará disponible en: `https://backend-equipo.onrender.com`

## Paso 6: Verificar el Despliegue

1. **Health Check**: Visita `https://tu-app.onrender.com/up`
   - Debe responder con un JSON: `{"status":"ok"}`

2. **Probar Endpoint**: Visita `https://tu-app.onrender.com/api/preguntas-secretas`
   - Debe devolver las preguntas secretas en formato JSON

3. **Revisar Logs**: En el panel de Render, verifica que no haya errores en los logs

## ⚠️ Notas Importantes

### MongoDB

- Asegúrate de que tu MongoDB Atlas permita conexiones desde cualquier IP (0.0.0.0/0)
- O agrega las IPs de Render a la whitelist de MongoDB

### Sleep después de Inactividad

- En el plan gratuito, Render pone a dormir el servicio después de 15 minutos de inactividad
- El primer request después del sleep puede tardar 30-60 segundos (cold start)

### Variables de Entorno

- Las variables marcadas con `sync: false` en `render.yaml` NO se sincronizan automáticamente
- Debes agregarlas manualmente en el panel de Render
- Las variables con `fromService` se generan automáticamente (como APP_URL)

### OAuth Redirect URIs

- Actualiza las URIs de redirección en Google/Facebook OAuth:
  - Google: `https://tu-app.onrender.com/auth/google/callback`
  - Facebook: `https://tu-app.onrender.com/auth/facebook/callback`

## 🔧 Solución de Problemas

### Error: "MongoDB extension not found"

El Dockerfile ya incluye la extensión de MongoDB. Si aún tienes problemas:
1. Verifica que estés usando el Dockerfile (no runtime: php)
2. Revisa los logs de build en Render

### Error: "APP_KEY not set"

1. Genera una clave: `php artisan key:generate --show`
2. Agrégala en las variables de entorno de Render

### Error: "Storage permissions"

El script `start.sh` intenta dar permisos automáticamente. Si hay problemas, puedes ejecutar en Render:
```bash
chmod -R 775 storage bootstrap/cache
```

### El servicio no inicia

1. Revisa los logs en Render
2. Verifica que todas las variables de entorno estén configuradas
3. Asegúrate de que MongoDB esté accesible
4. Verifica que el puerto $PORT esté configurado (Render lo asigna automáticamente)

## 📞 Ayuda Adicional

- Documentación completa: Ver `DESPLIEGUE_RENDER.md`
- Logs en tiempo real: Panel de Render → Logs
- Estado del servicio: Panel de Render → Overview

## ✅ Checklist Final

- [ ] Repositorio en GitHub actualizado
- [ ] Servicio creado en Render
- [ ] Variables de entorno configuradas
- [ ] MongoDB URI configurada y accesible
- [ ] APP_KEY generada y configurada
- [ ] Servicio desplegado y funcionando
- [ ] Health check responde correctamente
- [ ] Endpoints API funcionando
- [ ] OAuth configurado (si aplica)
- [ ] CORS configurado para el frontend

¡Listo! Tu backend está desplegado en Render 🎉

