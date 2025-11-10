# 🔐 Configurar JWT_SECRET en Render

## 📋 Resumen

JWT_SECRET es la clave secreta que se usa para firmar y verificar tokens JWT. Es importante configurarla manualmente en Render para que sea persistente entre reinicios.

## 🔑 Paso 1: Generar Clave JWT_SECRET

### Opción A: Usar clave generada
```
LaAYQ3IEjw4oDZJsRQpnSZcJWvii8OsXJOAuPWrP6SATBgk23dKcgrWqXfJOvlqj
```

### Opción B: Generar nueva clave (Linux/Mac)
```bash
openssl rand -base64 64 | head -c 64
```

### Opción C: Generar nueva clave (Windows PowerShell)
```powershell
[Convert]::ToBase64String((1..64 | ForEach-Object { Get-Random -Maximum 256 }))
```

## 🌐 Paso 2: Ir al Dashboard de Render

1. Ve a: https://dashboard.render.com
2. Inicia sesión con tu cuenta

## 🎯 Paso 3: Seleccionar el Servicio

1. En la lista de servicios, busca `backend-equipo`
2. Haz clic en el nombre del servicio

## ⚙️ Paso 4: Ir a Environment Variables

1. En el menú lateral izquierdo, busca la sección **"Environment"**
2. Haz clic en **"Environment"**
3. Verás la lista de variables de entorno actuales

## ➕ Paso 5: Agregar JWT_SECRET

1. Haz clic en el botón **"Add Environment Variable"** o **"Add Variable"**
2. En el campo **"Key"**, escribe: `JWT_SECRET`
3. En el campo **"Value"**, pega la clave generada (del Paso 1)
4. Haz clic en **"Save Changes"** o **"Save"**

## 🔄 Paso 6: Reiniciar el Servicio

Render reiniciará automáticamente el servicio después de guardar la variable.

Si no se reinicia automáticamente:
1. Ve a la pestaña **"Events"** o **"Deploys"**
2. Haz clic en **"Manual Deploy"** → **"Clear build cache & deploy"**

## ✅ Paso 7: Verificar la Configuración

1. Ve a la pestaña **"Logs"** en Render
2. Busca el mensaje:
   ```
   JWT_SECRET configurada correctamente desde variables de entorno
   ```
3. Si ves esta advertencia, significa que no se configuró correctamente:
   ```
   ADVERTENCIA: JWT_SECRET no está configurada
   ```

## 🎯 Ubicación Exacta en Render

```
Dashboard → Servicios → backend-equipo → Environment → Add Environment Variable
```

## 📝 Ejemplo de Configuración

```
Key:   JWT_SECRET
Value: LaAYQ3IEjw4oDZJsRQpnSZcJWvii8OsXJOAuPWrP6SATBgk23dKcgrWqXfJOvlqj
```

## ⚠️ Importante

- **No compartas** la clave JWT_SECRET públicamente
- **Guarda** la clave en un lugar seguro
- La clave debe ser de **al menos 32 caracteres**
- Si cambias la clave, todos los tokens JWT existentes se invalidarán

## 🔍 Verificar que Funciona

Después de configurar JWT_SECRET:

1. Intenta hacer login en tu aplicación
2. Verifica que recibes un token JWT
3. Usa el token para acceder a rutas protegidas
4. Si todo funciona, JWT_SECRET está configurada correctamente

## 🆘 Solución de Problemas

### Error: "Secret is not set"
- Verifica que JWT_SECRET esté en las variables de entorno de Render
- Verifica que el nombre sea exactamente `JWT_SECRET` (sin espacios)
- Reinicia el servicio manualmente

### Tokens JWT se invalidan después de reiniciar
- Esto significa que JWT_SECRET no está configurada en Render
- Sigue los pasos anteriores para configurarla

### No encuentro la sección "Environment"
- Busca "Environment Variables" o "Env" en el menú lateral
- O busca en la configuración del servicio

## 📞 Ayuda

Si tienes problemas:
1. Verifica los logs de Render
2. Verifica que la variable esté correctamente escrita
3. Reinicia el servicio manualmente
4. Contacta al soporte de Render si es necesario

