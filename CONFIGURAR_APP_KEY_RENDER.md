# 🔐 Configurar APP_KEY en Render

## 📋 Resumen

APP_KEY es la clave de cifrado de Laravel. Debe tener el formato `base64:...` y una longitud específica. Si no está configurada correctamente, verás el error: "Cifrado no compatible o longitud de clave incorrecta".

## 🔑 Paso 1: Generar APP_KEY

### Opción A: Generar localmente (Recomendado)

Ejecuta en tu terminal local:

```bash
php artisan key:generate --show
```

Esto generará una clave en el formato correcto:
```
base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

### Opción B: Generar manualmente (Linux/Mac)

```bash
echo "base64:$(openssl rand -base64 32)"
```

### Opción C: Generar manualmente (Windows PowerShell)

```powershell
"base64:" + [Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Maximum 256 }))
```

## 🌐 Paso 2: Verificar APP_KEY en Render

1. Ve a: https://dashboard.render.com
2. Selecciona el servicio `backend-equipo`
3. Ve a la sección **"Environment"**
4. Busca `APP_KEY` en la lista de variables
5. Verifica que:
   - Existe
   - Tiene el formato `base64:...`
   - Tiene una longitud de aproximadamente 60 caracteres

## ➕ Paso 3: Configurar APP_KEY en Render

### Si APP_KEY no existe:

1. Haz clic en **"Add Environment Variable"**
2. En el campo **"Key"**, escribe: `APP_KEY`
3. En el campo **"Value"**, pega la clave generada (del Paso 1)
4. Haz clic en **"Save Changes"**

### Si APP_KEY existe pero tiene formato incorrecto:

1. Haz clic en `APP_KEY` para editarla
2. Reemplaza el valor con una clave generada correctamente
3. Asegúrate de que tenga el formato `base64:...`
4. Haz clic en **"Save Changes"**

## 🔄 Paso 4: Reiniciar el Servicio

Render reiniciará automáticamente el servicio después de guardar la variable.

Si no se reinicia automáticamente:
1. Ve a la pestaña **"Events"** o **"Deploys"**
2. Haz clic en **"Manual Deploy"** → **"Clear build cache & deploy"**

## ✅ Paso 5: Verificar la Configuración

1. Ve a la pestaña **"Logs"** en Render
2. Busca el mensaje:
   ```
   APP_KEY configurada correctamente
   ```
3. Si ves este error, significa que APP_KEY no está configurada correctamente:
   ```
   ERROR: APP_KEY no pudo ser generada
   ```
   O:
   ```
   Cifrado no compatible o longitud de clave incorrecta
   ```

## 📝 Formato Correcto de APP_KEY

```
base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

- Debe empezar con `base64:`
- Debe tener aproximadamente 60 caracteres en total
- La parte después de `base64:` debe ser una cadena base64 válida de 32 bytes

## 🎯 Ejemplo de APP_KEY Válida

```
base64:8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q8Q==
```

## ⚠️ Importante

- **No compartas** APP_KEY públicamente
- **Guarda** la clave en un lugar seguro
- La clave debe tener el formato `base64:...`
- Si cambias APP_KEY, todos los datos cifrados existentes se invalidarán
- En Render, puedes usar `generateValue: true` pero es mejor configurarla manualmente

## 🔍 Verificar que Funciona

Después de configurar APP_KEY:

1. Verifica los logs de Render
2. Busca el mensaje "APP_KEY configurada correctamente"
3. Intenta hacer login en tu aplicación
4. Si no hay errores de cifrado, APP_KEY está configurada correctamente

## 🆘 Solución de Problemas

### Error: "Cifrado no compatible o longitud de clave incorrecta"

**Causa**: APP_KEY no tiene el formato correcto o la longitud incorrecta.

**Solución**:
1. Genera una nueva APP_KEY usando `php artisan key:generate --show`
2. Copia la clave generada
3. Configúrala en Render como se describe arriba
4. Reinicia el servicio

### Error: "APP_KEY no pudo ser generada"

**Causa**: El script no pudo generar APP_KEY automáticamente.

**Solución**:
1. Configura APP_KEY manualmente en Render
2. Asegúrate de que tenga el formato `base64:...`
3. Reinicia el servicio

### APP_KEY se regenera en cada reinicio

**Causa**: APP_KEY no está configurada en Render, el script la genera automáticamente.

**Solución**:
1. Configura APP_KEY manualmente en Render
2. Esto asegurará que la clave sea persistente entre reinicios

## 📞 Configuración en render.yaml

En `render.yaml`, puedes configurar APP_KEY de dos formas:

### Opción 1: Generar automáticamente (no recomendado)
```yaml
- key: APP_KEY
  generateValue: true
```

### Opción 2: Configurar manualmente (recomendado)
```yaml
- key: APP_KEY
  sync: false
```

Luego configura APP_KEY manualmente en el dashboard de Render.

## 🎯 Ruta en Render

```
Dashboard → Servicios → backend-equipo → Environment → APP_KEY
```

