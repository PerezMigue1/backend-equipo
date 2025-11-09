# ✅ Cómo Verificar que las APIs Funcionan en Render

## 🔍 Métodos de Verificación

### 1. Health Check (Verificación Básica)

**Endpoint:** `https://backend-equipo.onrender.com/up`

**Método:** GET

**Desde el navegador:**
- Abre: `https://backend-equipo.onrender.com/up`
- Debe responder con: `{"status":"ok"}` o similar

**Desde la terminal (PowerShell):**
```powershell
curl https://backend-equipo.onrender.com/up
```

**Desde la terminal (Git Bash/WSL):**
```bash
curl https://backend-equipo.onrender.com/up
```

**Respuesta esperada:**
```json
{"status":"ok"}
```

Si responde correctamente, el servidor está funcionando ✅

---

### 2. Endpoint de Preguntas Secretas (API Pública)

**Endpoint:** `https://backend-equipo.onrender.com/api/preguntas-secretas`

**Método:** GET

**Desde el navegador:**
- Abre: `https://backend-equipo.onrender.com/api/preguntas-secretas`
- Debe mostrar las preguntas secretas en formato JSON

**Desde PowerShell:**
```powershell
Invoke-WebRequest -Uri "https://backend-equipo.onrender.com/api/preguntas-secretas" -UseBasicParsing | Select-Object -ExpandProperty Content
```

**Desde curl:**
```bash
curl https://backend-equipo.onrender.com/api/preguntas-secretas
```

**Respuesta esperada:**
```json
{
  "preguntas": [
    "¿Cuál es el nombre de tu mascota?",
    "¿Cuál es el nombre de tu ciudad natal?",
    "¿Cuál es el nombre de tu mejor amigo?",
    ...
  ]
}
```

---

### 3. Endpoint de Registro (API Pública)

**Endpoint:** `https://backend-equipo.onrender.com/api/register`

**Método:** POST

**Desde PowerShell:**
```powershell
$body = @{
    name = "Usuario Prueba"
    email = "test@example.com"
    password = "password123"
    password_confirmation = "password123"
    pregunta_secreta = "¿Cuál es el nombre de tu mascota?"
    respuesta_secreta = "Fido"
} | ConvertTo-Json

Invoke-RestMethod -Uri "https://backend-equipo.onrender.com/api/register" -Method POST -Body $body -ContentType "application/json"
```

**Desde curl:**
```bash
curl -X POST https://backend-equipo.onrender.com/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Usuario Prueba",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "pregunta_secreta": "¿Cuál es el nombre de tu mascota?",
    "respuesta_secreta": "Fido"
  }'
```

**Respuesta esperada (éxito):**
```json
{
  "user": {
    "id": "...",
    "name": "Usuario Prueba",
    "email": "test@example.com"
  },
  "token": "1|..."
}
```

---

### 4. Endpoint de Login (API Pública)

**Endpoint:** `https://backend-equipo.onrender.com/api/login`

**Método:** POST

**Desde PowerShell:**
```powershell
$body = @{
    email = "test@example.com"
    password = "password123"
} | ConvertTo-Json

Invoke-RestMethod -Uri "https://backend-equipo.onrender.com/api/login" -Method POST -Body $body -ContentType "application/json"
```

**Desde curl:**
```bash
curl -X POST https://backend-equipo.onrender.com/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Respuesta esperada (éxito):**
```json
{
  "user": {
    "id": "...",
    "name": "Usuario Prueba",
    "email": "test@example.com"
  },
  "token": "1|..."
}
```

---

### 5. Endpoint de Usuario Actual (API Protegida)

**Endpoint:** `https://backend-equipo.onrender.com/api/user`

**Método:** GET

**Requiere:** Token de autenticación

**Desde PowerShell:**
```powershell
$token = "TU_TOKEN_AQUI"
$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
}

Invoke-RestMethod -Uri "https://backend-equipo.onrender.com/api/user" -Method GET -Headers $headers
```

**Desde curl:**
```bash
curl -X GET https://backend-equipo.onrender.com/api/user \
  -H "Authorization: Bearer TU_TOKEN_AQUI" \
  -H "Accept: application/json"
```

**Respuesta esperada:**
```json
{
  "id": "...",
  "name": "Usuario Prueba",
  "email": "test@example.com"
}
```

---

## 📊 Verificar en Render Dashboard

### 1. Revisar el Estado del Servicio

1. Ve a https://dashboard.render.com
2. Selecciona el servicio `backend-equipo`
3. Verifica que el estado sea **"Live"** (verde)

### 2. Revisar los Logs

1. En el servicio, haz clic en **"Logs"**
2. Busca mensajes como:
   - `=== Iniciando aplicacion Laravel ===`
   - `=== Iniciando servidor en puerto $PORT ===`
   - Sin errores de conexión a MongoDB
   - Sin errores de permisos

### 3. Verificar las Variables de Entorno

1. Haz clic en **"Environment"**
2. Verifica que todas las variables estén configuradas:
   - `APP_URL`: `https://backend-equipo.onrender.com`
   - `MONGODB_URI`: Configurado
   - `FRONTEND_URL`: `https://modulo-usuario.netlify.app`
   - `GOOGLE_REDIRECT_URI`: `https://backend-equipo.onrender.com/auth/google/callback`

---

## 🧪 Script de Prueba Completo (PowerShell)

Crea un archivo `test-api.ps1`:

```powershell
$baseUrl = "https://backend-equipo.onrender.com"

Write-Host "=== Verificando APIs en Render ===" -ForegroundColor Green

# 1. Health Check
Write-Host "`n1. Health Check..." -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$baseUrl/up" -Method GET
    Write-Host "✅ Health Check OK: $($response | ConvertTo-Json)" -ForegroundColor Green
} catch {
    Write-Host "❌ Health Check Falló: $($_.Exception.Message)" -ForegroundColor Red
}

# 2. Preguntas Secretas
Write-Host "`n2. Preguntas Secretas..." -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$baseUrl/api/preguntas-secretas" -Method GET
    Write-Host "✅ Preguntas Secretas OK: $($response.preguntas.Count) preguntas encontradas" -ForegroundColor Green
} catch {
    Write-Host "❌ Preguntas Secretas Falló: $($_.Exception.Message)" -ForegroundColor Red
}

# 3. Test de Registro
Write-Host "`n3. Test de Registro..." -ForegroundColor Yellow
$testEmail = "test$(Get-Random)@example.com"
$registerBody = @{
    name = "Usuario Test"
    email = $testEmail
    password = "password123"
    password_confirmation = "password123"
    pregunta_secreta = "¿Cuál es el nombre de tu mascota?"
    respuesta_secreta = "Fido"
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri "$baseUrl/api/register" -Method POST -Body $registerBody -ContentType "application/json"
    Write-Host "✅ Registro OK: Usuario creado: $testEmail" -ForegroundColor Green
    $token = $response.token
    Write-Host "   Token: $($token.Substring(0, 20))..." -ForegroundColor Gray
    
    # 4. Test de Usuario Actual (con token)
    Write-Host "`n4. Usuario Actual (con token)..." -ForegroundColor Yellow
    $headers = @{
        "Authorization" = "Bearer $token"
        "Accept" = "application/json"
    }
    try {
        $userResponse = Invoke-RestMethod -Uri "$baseUrl/api/user" -Method GET -Headers $headers
        Write-Host "✅ Usuario Actual OK: $($userResponse.email)" -ForegroundColor Green
    } catch {
        Write-Host "❌ Usuario Actual Falló: $($_.Exception.Message)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Registro Falló: $($_.Exception.Message)" -ForegroundColor Red
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "   Respuesta: $responseBody" -ForegroundColor Gray
    }
}

Write-Host "`n=== Verificación Completa ===" -ForegroundColor Green
```

**Ejecutar:**
```powershell
.\test-api.ps1
```

---

## 🌐 Probar desde el Navegador

### Endpoints que puedes probar directamente:

1. **Health Check:**
   - `https://backend-equipo.onrender.com/up`

2. **Preguntas Secretas:**
   - `https://backend-equipo.onrender.com/api/preguntas-secretas`

3. **OAuth Google (redirige a Google):**
   - `https://backend-equipo.onrender.com/auth/google`

---

## 🐛 Solución de Problemas

### Si el Health Check no responde:

1. **Verifica que el servicio esté "Live"** en Render
2. **Revisa los logs** en Render para ver errores
3. **Verifica que el puerto $PORT esté configurado** (Render lo asigna automáticamente)
4. **Espera 30-60 segundos** si el servicio estaba dormido (plan gratuito)

### Si las APIs responden con errores:

1. **Revisa los logs** en Render Dashboard
2. **Verifica las variables de entorno** (especialmente `MONGODB_URI`)
3. **Verifica que MongoDB esté accesible** desde Render
4. **Revisa que CORS esté configurado** correctamente

### Si hay errores de CORS:

1. Verifica que `CORS_ALLOWED_ORIGINS` incluya tu frontend
2. Verifica que `FRONTEND_URL` esté correcto
3. Reinicia el servicio después de cambiar las variables

### Si hay errores de autenticación:

1. Verifica que `APP_KEY` esté configurado
2. Verifica que las rutas de OAuth estén correctas
3. Revisa los logs para errores específicos

---

## 📝 Checklist de Verificación

- [ ] Servicio está "Live" en Render
- [ ] Health check (`/up`) responde correctamente
- [ ] Endpoint de preguntas secretas funciona
- [ ] Endpoint de registro funciona
- [ ] Endpoint de login funciona
- [ ] Endpoint de usuario actual funciona (con token)
- [ ] OAuth de Google redirige correctamente
- [ ] No hay errores en los logs
- [ ] Variables de entorno están configuradas correctamente
- [ ] MongoDB está conectado (verificar en logs)
- [ ] CORS permite requests del frontend

---

## 🔗 URLs de Prueba Rápida

- Health Check: https://backend-equipo.onrender.com/up
- Preguntas Secretas: https://backend-equipo.onrender.com/api/preguntas-secretas
- OAuth Google: https://backend-equipo.onrender.com/auth/google

---

## 💡 Tips

1. **Primera vez después del sleep:** El primer request después de que el servicio "despierte" puede tardar 30-60 segundos
2. **Logs en tiempo real:** Puedes ver los logs en tiempo real en Render Dashboard
3. **Herramientas útiles:**
   - Postman para probar APIs
   - curl para pruebas rápidas
   - Navegador para endpoints GET
   - PowerShell/curl para endpoints POST

---

## ✅ Si Todo Funciona

Si todos los endpoints responden correctamente:
- ✅ Tu backend está funcionando correctamente en Render
- ✅ Las APIs están disponibles públicamente
- ✅ La conexión a MongoDB está funcionando
- ✅ La autenticación está configurada correctamente
- ✅ CORS está configurado para permitir requests del frontend

¡Tu backend está listo para usar! 🎉

