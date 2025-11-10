# 🔗 Lista Completa de Endpoints - Render

**URL Base:** `https://backend-equipo.onrender.com`

---

## 📋 Índice

1. [Endpoints Públicos (Sin Autenticación)](#endpoints-públicos)
2. [Endpoints de Autenticación](#endpoints-de-autenticación)
3. [Endpoints Protegidos (Requieren Token)](#endpoints-protegidos)
4. [Endpoints de OAuth](#endpoints-de-oauth)
5. [Endpoints de Recuperación de Contraseña](#endpoints-de-recuperación-de-contraseña)
6. [Endpoints de Consulta](#endpoints-de-consulta)
7. [Health Check](#health-check)

---

## 🌐 Endpoints Públicos (Sin Autenticación)

### 1. Registro de Usuario
- **Método:** `POST`
- **URL:** `https://backend-equipo.onrender.com/api/register`
- **Descripción:** Registra un nuevo usuario en el sistema
- **Body:**
  ```json
  {
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "pregunta_secreta": "¿Cuál es el nombre de tu primera mascota?",
    "respuesta_secreta": "Max"
  }
  ```
- **Respuesta:** Usuario creado + token de autenticación

### 2. Login
- **Método:** `POST`
- **URL:** `https://backend-equipo.onrender.com/api/login`
- **Descripción:** Inicia sesión con email y contraseña
- **Body:**
  ```json
  {
    "email": "juan@example.com",
    "password": "password123"
  }
  ```
- **Respuesta:** Usuario + token de autenticación

### 3. Obtener Preguntas Secretas
- **Método:** `GET`
- **URL:** `https://backend-equipo.onrender.com/api/preguntas-secretas`
- **Descripción:** Obtiene todas las preguntas secretas disponibles
- **Respuesta:**
  ```json
  {
    "preguntas": [
      {
        "_id": "...",
        "pregunta": "¿Cuál es el nombre de tu primera mascota?"
      }
    ],
    "total": 10
  }
  ```

### 4. Listar Usuarios
- **Método:** `GET`
- **URL:** `https://backend-equipo.onrender.com/api/usuarios/list`
- **Descripción:** Lista todos los usuarios de la colección `usuario`
- **Respuesta:**
  ```json
  {
    "total": 10,
    "coleccion": "usuario",
    "base_datos": "equipo",
    "usuarios": [...]
  }
  ```

---

## 🔐 Endpoints de Autenticación

### 5. Obtener Usuario Autenticado
- **Método:** `GET`
- **URL:** `https://backend-equipo.onrender.com/api/user`
- **Autenticación:** Requerida (Bearer Token)
- **Headers:**
  ```
  Authorization: Bearer {token}
  ```
- **Descripción:** Obtiene la información del usuario autenticado
- **Respuesta:** Datos del usuario (sin password)

### 6. Logout
- **Método:** `POST`
- **URL:** `https://backend-equipo.onrender.com/api/logout`
- **Autenticación:** Requerida (Bearer Token)
- **Headers:**
  ```
  Authorization: Bearer {token}
  ```
- **Descripción:** Cierra sesión y elimina el token de autenticación
- **Respuesta:** Mensaje de confirmación

---

## 🔑 Endpoints de OAuth

### 7. Login con Google - Iniciar
- **Método:** `GET`
- **URL:** `https://backend-equipo.onrender.com/auth/google`
- **Descripción:** Redirige a Google para autenticación
- **Redirección:** Google OAuth → Callback

### 8. Login con Google - Callback
- **Método:** `GET`
- **URL:** `https://backend-equipo.onrender.com/auth/google/callback`
- **Descripción:** Callback de Google OAuth (crea/actualiza usuario)
- **Redirección:** Frontend con token
- **Parámetros:** `token`, `provider=google`

### 9. Login con Facebook - Iniciar
- **Método:** `GET`
- **URL:** `https://backend-equipo.onrender.com/auth/facebook`
- **Descripción:** Redirige a Facebook para autenticación
- **Redirección:** Facebook OAuth → Callback

### 10. Login con Facebook - Callback
- **Método:** `GET`
- **URL:** `https://backend-equipo.onrender.com/auth/facebook/callback`
- **Descripción:** Callback de Facebook OAuth (crea/actualiza usuario)
- **Redirección:** Frontend con token
- **Parámetros:** `token`, `provider=facebook`

---

## 🔒 Endpoints de Recuperación de Contraseña

### 11. Verificar Email
- **Método:** `POST`
- **URL:** `https://backend-equipo.onrender.com/api/password/verify-email`
- **Descripción:** Verifica que el email existe y devuelve la pregunta secreta
- **Body:**
  ```json
  {
    "email": "juan@example.com"
  }
  ```
- **Respuesta:**
  ```json
  {
    "email": "juan@example.com",
    "pregunta_secreta": "¿Cuál es el nombre de tu primera mascota?"
  }
  ```

### 12. Verificar Respuesta Secreta
- **Método:** `POST`
- **URL:** `https://backend-equipo.onrender.com/api/password/verify-answer`
- **Descripción:** Verifica que la respuesta secreta sea correcta
- **Body:**
  ```json
  {
    "email": "juan@example.com",
    "respuesta_secreta": "Max"
  }
  ```
- **Respuesta:** Confirmación de respuesta correcta

### 13. Actualizar Contraseña
- **Método:** `POST`
- **URL:** `https://backend-equipo.onrender.com/api/password/update`
- **Descripción:** Actualiza la contraseña del usuario
- **Body:**
  ```json
  {
    "email": "juan@example.com",
    "new_password": "nueva_password123",
    "new_password_confirmation": "nueva_password123",
    "respuesta_secreta": "Max"
  }
  ```
- **Respuesta:** Confirmación de actualización

---

## 📊 Endpoints de Consulta

### 14. Listar Preguntas Secretas
- **Método:** `GET`
- **URL:** `https://backend-equipo.onrender.com/api/preguntas-secretas`
- **Descripción:** Lista todas las preguntas secretas disponibles
- **Colección MongoDB:** `recuperar-password`
- **Respuesta:** Array de preguntas con `_id` y `pregunta`

### 15. Listar Usuarios
- **Método:** `GET`
- **URL:** `https://backend-equipo.onrender.com/api/usuarios/list`
- **Descripción:** Lista todos los usuarios del sistema
- **Colección MongoDB:** `usuario`
- **Respuesta:** Array de usuarios con todos sus datos

---

## 🏥 Health Check

### 16. Health Check
- **Método:** `GET`
- **URL:** `https://backend-equipo.onrender.com/up`
- **Descripción:** Verifica que el servidor está funcionando
- **Uso:** Render.com lo usa para verificar el estado del servicio

---

## 📝 Endpoints Adicionales (Facebook Developer)

### 17. Privacy Policy
- **Método:** `GET`
- **URL:** `https://backend-equipo.onrender.com/privacy`
- **Descripción:** Política de privacidad (requerida por Facebook Developer)
- **Respuesta:** JSON con mensaje

### 18. Data Deletion
- **Método:** `GET`
- **URL:** `https://backend-equipo.onrender.com/delete-data`
- **Descripción:** Endpoint para eliminación de datos (requerida por Facebook Developer)
- **Respuesta:** JSON con mensaje

---

## 🔐 Autenticación con Token

Para los endpoints protegidos, incluye el token en el header:

```
Authorization: Bearer {tu_token_aqui}
```

El token se obtiene de:
- `POST /api/login` → `token` en la respuesta
- `POST /api/register` → `token` en la respuesta
- `GET /auth/google/callback` → `token` en la URL de redirección
- `GET /auth/facebook/callback` → `token` en la URL de redirección

---

## 📋 Resumen Rápido

| # | Método | Endpoint | Autenticación | Descripción |
|---|--------|----------|---------------|-------------|
| 1 | POST | `/api/register` | ❌ | Registro de usuario |
| 2 | POST | `/api/login` | ❌ | Login con email/password |
| 3 | GET | `/api/preguntas-secretas` | ❌ | Listar preguntas secretas |
| 4 | GET | `/api/usuarios/list` | ❌ | Listar todos los usuarios |
| 5 | GET | `/api/user` | ✅ | Obtener usuario autenticado |
| 6 | POST | `/api/logout` | ✅ | Cerrar sesión |
| 7 | GET | `/auth/google` | ❌ | Iniciar OAuth Google |
| 8 | GET | `/auth/google/callback` | ❌ | Callback Google OAuth |
| 9 | GET | `/auth/facebook` | ❌ | Iniciar OAuth Facebook |
| 10 | GET | `/auth/facebook/callback` | ❌ | Callback Facebook OAuth |
| 11 | POST | `/api/password/verify-email` | ❌ | Verificar email |
| 12 | POST | `/api/password/verify-answer` | ❌ | Verificar respuesta secreta |
| 13 | POST | `/api/password/update` | ❌ | Actualizar contraseña |
| 14 | GET | `/api/preguntas-secretas` | ❌ | Listar preguntas (duplicado) |
| 15 | GET | `/api/usuarios/list` | ❌ | Listar usuarios (duplicado) |
| 16 | GET | `/up` | ❌ | Health check |
| 17 | GET | `/privacy` | ❌ | Privacy policy |
| 18 | GET | `/delete-data` | ❌ | Data deletion |

---

## 🧪 Ejemplos de Uso

### Ejemplo 1: Registro
```bash
curl -X POST https://backend-equipo.onrender.com/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "pregunta_secreta": "¿Cuál es el nombre de tu primera mascota?",
    "respuesta_secreta": "Max"
  }'
```

### Ejemplo 2: Login
```bash
curl -X POST https://backend-equipo.onrender.com/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "juan@example.com",
    "password": "password123"
  }'
```

### Ejemplo 3: Obtener Usuario (con token)
```bash
curl -X GET https://backend-equipo.onrender.com/api/user \
  -H "Authorization: Bearer {tu_token}"
```

### Ejemplo 4: Listar Preguntas Secretas
```bash
curl -X GET https://backend-equipo.onrender.com/api/preguntas-secretas
```

### Ejemplo 5: Listar Usuarios
```bash
curl -X GET https://backend-equipo.onrender.com/api/usuarios/list
```

---

## 🔗 URLs Completas

### API Endpoints
- `https://backend-equipo.onrender.com/api/register`
- `https://backend-equipo.onrender.com/api/login`
- `https://backend-equipo.onrender.com/api/user`
- `https://backend-equipo.onrender.com/api/logout`
- `https://backend-equipo.onrender.com/api/preguntas-secretas`
- `https://backend-equipo.onrender.com/api/usuarios/list`
- `https://backend-equipo.onrender.com/api/password/verify-email`
- `https://backend-equipo.onrender.com/api/password/verify-answer`
- `https://backend-equipo.onrender.com/api/password/update`

### OAuth Endpoints
- `https://backend-equipo.onrender.com/auth/google`
- `https://backend-equipo.onrender.com/auth/google/callback`
- `https://backend-equipo.onrender.com/auth/facebook`
- `https://backend-equipo.onrender.com/auth/facebook/callback`

### Otros
- `https://backend-equipo.onrender.com/up`
- `https://backend-equipo.onrender.com/privacy`
- `https://backend-equipo.onrender.com/delete-data`

---

## 📚 Notas

- **Base URL:** Todas las URLs usan `https://backend-equipo.onrender.com`
- **Autenticación:** Los endpoints protegidos requieren `Authorization: Bearer {token}`
- **Content-Type:** Para POST, usar `application/json`
- **CORS:** Configurado para permitir requests desde `https://modulo-usuario.netlify.app`
- **Colecciones MongoDB:** 
  - `usuario` - Almacena usuarios
  - `recuperar-password` - Almacena preguntas secretas disponibles

---

## ✅ Estado

Todos los endpoints están **funcionando correctamente** y listos para usar en producción.

