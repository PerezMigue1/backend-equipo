# 🔗 URLs de la API - Colección de Usuarios

## 🌐 URL Base

**Producción (Render):**
```
https://backend-equipo.onrender.com
```

**Desarrollo Local:**
```
http://localhost:8000
```

---

## 👤 Endpoints de Usuarios

### 1. **Registrar Usuario (Crear)**
**URL:** `POST /api/register`

**URL Completa:**
- Producción: `https://backend-equipo.onrender.com/api/register`
- Local: `http://localhost:8000/api/register`

**Body (JSON):**
```json
{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "pregunta_secreta": "¿Cuál es el nombre de tu mascota?",
  "respuesta_secreta": "Fido"
}
```

**Respuesta:**
```json
{
  "user": {
    "_id": "...",
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "pregunta_secreta": {
      "pregunta": "¿Cuál es el nombre de tu mascota?",
      "respuesta": "Fido"
    }
  },
  "token": "1|...",
  "message": "Registration successful"
}
```

---

### 2. **Login (Autenticarse)**
**URL:** `POST /api/login`

**URL Completa:**
- Producción: `https://backend-equipo.onrender.com/api/login`
- Local: `http://localhost:8000/api/login`

**Body (JSON):**
```json
{
  "email": "juan@example.com",
  "password": "password123"
}
```

**Respuesta:**
```json
{
  "user": {
    "_id": "...",
    "name": "Juan Pérez",
    "email": "juan@example.com"
  },
  "token": "1|..."
}
```

---

### 3. **Obtener Usuario Actual (Autenticado)**
**URL:** `GET /api/user`

**URL Completa:**
- Producción: `https://backend-equipo.onrender.com/api/user`
- Local: `http://localhost:8000/api/user`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Respuesta:**
```json
{
  "_id": "...",
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "pregunta_secreta": {
    "pregunta": "¿Cuál es el nombre de tu mascota?",
    "respuesta": "Fido"
  }
}
```

**⚠️ Requiere autenticación (token)**

---

### 4. **Logout (Cerrar Sesión)**
**URL:** `POST /api/logout`

**URL Completa:**
- Producción: `https://backend-equipo.onrender.com/api/logout`
- Local: `http://localhost:8000/api/logout`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Respuesta:**
```json
{
  "message": "Logged out successfully"
}
```

**⚠️ Requiere autenticación (token)**

---

### 5. **Verificar Email (Recuperación de Contraseña)**
**URL:** `POST /api/password/verify-email`

**URL Completa:**
- Producción: `https://backend-equipo.onrender.com/api/password/verify-email`
- Local: `http://localhost:8000/api/password/verify-email`

**Body (JSON):**
```json
{
  "email": "juan@example.com"
}
```

**Respuesta:**
```json
{
  "email": "juan@example.com",
  "pregunta_secreta": "¿Cuál es el nombre de tu mascota?"
}
```

---

### 6. **Verificar Respuesta Secreta**
**URL:** `POST /api/password/verify-answer`

**URL Completa:**
- Producción: `https://backend-equipo.onrender.com/api/password/verify-answer`
- Local: `http://localhost:8000/api/password/verify-answer`

**Body (JSON):**
```json
{
  "email": "juan@example.com",
  "respuesta_secreta": "Fido"
}
```

**Respuesta:**
```json
{
  "message": "Respuesta correcta. Puede proceder a cambiar la contraseña.",
  "verified": true
}
```

---

### 7. **Actualizar Contraseña**
**URL:** `POST /api/password/update`

**URL Completa:**
- Producción: `https://backend-equipo.onrender.com/api/password/update`
- Local: `http://localhost:8000/api/password/update`

**Body (JSON):**
```json
{
  "email": "juan@example.com",
  "new_password": "nuevaPassword123",
  "new_password_confirmation": "nuevaPassword123",
  "respuesta_secreta": "Fido"
}
```

**Respuesta:**
```json
{
  "message": "Contraseña actualizada exitosamente."
}
```

---

## 🔐 OAuth (Autenticación Social)

### 8. **Login con Google**
**URL:** `GET /auth/google`

**URL Completa:**
- Producción: `https://backend-equipo.onrender.com/auth/google`
- Local: `http://localhost:8000/auth/google`

**Callback:**
- Producción: `https://backend-equipo.onrender.com/auth/google/callback`
- Local: `http://localhost:8000/auth/google/callback`

---

### 9. **Login con Facebook**
**URL:** `GET /auth/facebook`

**URL Completa:**
- Producción: `https://backend-equipo.onrender.com/auth/facebook`
- Local: `http://localhost:8000/auth/facebook`

**Callback:**
- Producción: `https://backend-equipo.onrender.com/auth/facebook/callback`
- Local: `http://localhost:8000/auth/facebook/callback`

---

## 📊 Resumen de Endpoints

| Método | Endpoint | Autenticación | Descripción |
|--------|----------|---------------|-------------|
| POST | `/api/register` | ❌ No | Registrar nuevo usuario |
| POST | `/api/login` | ❌ No | Iniciar sesión |
| GET | `/api/user` | ✅ Sí | Obtener usuario actual |
| POST | `/api/logout` | ✅ Sí | Cerrar sesión |
| POST | `/api/password/verify-email` | ❌ No | Verificar email para recuperación |
| POST | `/api/password/verify-answer` | ❌ No | Verificar respuesta secreta |
| POST | `/api/password/update` | ❌ No | Actualizar contraseña |
| GET | `/auth/google` | ❌ No | Login con Google |
| GET | `/auth/facebook` | ❌ No | Login con Facebook |

---

## 🧪 Ejemplos de Uso

### Crear Usuario (PowerShell)
```powershell
$body = @{
    name = "Juan Pérez"
    email = "juan@example.com"
    password = "password123"
    password_confirmation = "password123"
    pregunta_secreta = "¿Cuál es el nombre de tu mascota?"
    respuesta_secreta = "Fido"
} | ConvertTo-Json

Invoke-RestMethod -Uri "https://backend-equipo.onrender.com/api/register" -Method POST -Body $body -ContentType "application/json"
```

### Login (PowerShell)
```powershell
$body = @{
    email = "juan@example.com"
    password = "password123"
} | ConvertTo-Json

$response = Invoke-RestMethod -Uri "https://backend-equipo.onrender.com/api/login" -Method POST -Body $body -ContentType "application/json"
$token = $response.token
```

### Obtener Usuario Actual (PowerShell)
```powershell
$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
}

Invoke-RestMethod -Uri "https://backend-equipo.onrender.com/api/user" -Method GET -Headers $headers
```

### Login (curl)
```bash
curl -X POST https://backend-equipo.onrender.com/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "juan@example.com",
    "password": "password123"
  }'
```

### Obtener Usuario Actual (curl)
```bash
curl -X GET https://backend-equipo.onrender.com/api/user \
  -H "Authorization: Bearer TU_TOKEN_AQUI" \
  -H "Accept: application/json"
```

---

## ⚠️ Notas Importantes

1. **No hay endpoint para listar todos los usuarios** - Por seguridad, no existe un endpoint que devuelva todos los usuarios.

2. **No hay endpoint para actualizar usuario** - Solo se puede actualizar la contraseña mediante recuperación.

3. **No hay endpoint para eliminar usuario** - Esta funcionalidad no está implementada.

4. **Autenticación requerida:** Los endpoints `/api/user` y `/api/logout` requieren un token de autenticación en el header `Authorization: Bearer {token}`.

5. **Colección MongoDB:** Los usuarios se almacenan en la colección `usuario` en MongoDB.

---

## 🔗 URLs Completas (Producción)

- Registrar: `https://backend-equipo.onrender.com/api/register`
- Login: `https://backend-equipo.onrender.com/api/login`
- Usuario Actual: `https://backend-equipo.onrender.com/api/user`
- Logout: `https://backend-equipo.onrender.com/api/logout`
- Verificar Email: `https://backend-equipo.onrender.com/api/password/verify-email`
- Verificar Respuesta: `https://backend-equipo.onrender.com/api/password/verify-answer`
- Actualizar Contraseña: `https://backend-equipo.onrender.com/api/password/update`
- Google OAuth: `https://backend-equipo.onrender.com/auth/google`
- Facebook OAuth: `https://backend-equipo.onrender.com/auth/facebook`

---

## 📝 Estructura de Datos del Usuario

```json
{
  "_id": "ObjectId",
  "name": "string",
  "email": "string",
  "password": "string (hasheado)",
  "pregunta_secreta": {
    "pregunta": "string",
    "respuesta": "string"
  },
  "google_id": "string (opcional)",
  "facebook_id": "string (opcional)",
  "two_factor_secret": "string (opcional)",
  "two_factor_recovery_codes": "string (opcional)",
  "created_at": "ISODate",
  "updated_at": "ISODate"
}
```

