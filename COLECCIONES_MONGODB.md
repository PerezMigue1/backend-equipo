# 📊 Colecciones MongoDB Requeridas

## ✅ Colecciones Necesarias

Este backend Laravel solo requiere **2 colecciones** en MongoDB:

### 1. `usuario` 
**Uso:** Almacena los usuarios del sistema
**Modelo:** `App\Models\User`
**Conexión:** `mongodb`
**Campos principales:**
- `_id` (ObjectId)
- `name` (string)
- `email` (string, único)
- `password` (string, hasheado)
- `pregunta_secreta` (string JSON: `{"pregunta":"...","respuesta":"..."}`)
- `remember_token` (string, opcional)
- `google_id` (string, opcional)
- `facebook_id` (string, opcional)
- `two_factor_secret` (string, opcional)
- `two_factor_recovery_codes` (string, opcional)

### 2. `recuperar-password`
**Uso:** Almacena las preguntas secretas disponibles
**Controlador:** `App\Http\Controllers\Api\SecretQuestionController`
**Conexión:** Directa a MongoDB (no usa Eloquent)
**Estructura:** Array de documentos con las preguntas secretas

---

## ⚙️ Configuración que NO Requiere MongoDB

Las siguientes funcionalidades están configuradas para **NO usar MongoDB**:

### ✅ Cache
- **Driver:** `file`
- **Ubicación:** `storage/framework/cache/data`
- **No requiere colección en MongoDB**

### ✅ Sessions
- **Driver:** `file`
- **Ubicación:** `storage/framework/sessions`
- **No requiere colección en MongoDB**

### ✅ Queue
- **Driver:** `sync`
- **No requiere colección en MongoDB**
- Las tareas se ejecutan de forma síncrona

---

## 🚫 Colecciones que NO se Necesitan

Las siguientes colecciones/tablas **NO son necesarias** para este backend:

- ❌ `cache` - No se usa (cache en archivos)
- ❌ `cache_locks` - No se usa (cache en archivos)
- ❌ `jobs` - No se usa (queue sincrónico)
- ❌ `job_batches` - No se usa (queue sincrónico)
- ❌ `failed_jobs` - No se usa (queue sincrónico)
- ❌ `sessions` - No se usa (sessions en archivos)

---

## 📝 Migraciones

Las migraciones en `database/migrations/` están diseñadas para SQL (MySQL/PostgreSQL), pero **NO se ejecutan automáticamente** en MongoDB.

Estas migraciones son para referencia y **no afectan** el funcionamiento del backend con MongoDB.

**Importante:** Las colecciones `usuario` y `recuperar-password` deben crearse manualmente en MongoDB o se crearán automáticamente cuando se inserten los primeros documentos.

---

## ✅ Verificación

Para verificar que solo se usan estas 2 colecciones:

1. **Verificar modelo User:**
   - Archivo: `app/Models/User.php`
   - Colección: `usuario` ✅

2. **Verificar preguntas secretas:**
   - Archivo: `app/Http/Controllers/Api/SecretQuestionController.php`
   - Colección: `recuperar-password` ✅

3. **Verificar configuración:**
   - Cache: `config/cache.php` → `file` ✅
   - Session: `config/session.php` → `file` ✅
   - Queue: `config/queue.php` → `sync` ✅

---

## 🔍 Estructura de Datos

### Colección `usuario`
```json
{
  "_id": ObjectId("..."),
  "name": "francisco",
  "email": "valdesfrancis768@gmail.com",
  "password": "$2y$12$...",
  "pregunta_secreta": "{\"pregunta\":\"¿Cuál es el nombre de tu primera mascota?\",\"respuesta\":\"Doki\"}",
  "remember_token": "...",
  "created_at": ISODate("2025-10-30T01:14:00.479Z"),
  "updated_at": ISODate("2025-10-30T01:15:02.479Z")
}
```

**Nota:** `pregunta_secreta` se almacena como **string JSON** con caracteres Unicode escapados (ej: `\u00bf` para `¿`). El modelo User convierte automáticamente entre string JSON y array usando los accessors/mutators.

**Ejemplo real:**
```json
"pregunta_secreta": "{\"pregunta\":\"\\u00bfCu\\u00e1l es el nombre de tu primera mascota?\",\"respuesta\":\"Doki\"}"
```

Cuando se accede desde el código PHP, se convierte automáticamente a:
```php
['pregunta' => '¿Cuál es el nombre de tu primera mascota?', 'respuesta' => 'Doki']
```

### Colección `recuperar-password`
```json
{
  "_id": ObjectId("..."),
  "pregunta": "¿Cuál fue tu primera escuela?"
}
```

**Nota:** Esta colección contiene documentos simples con solo `_id` y `pregunta`. No hay campos adicionales como `activo`.

---

## 🚀 En Producción (Render)

En Render, las variables de entorno están configuradas para:

- ✅ **Cache:** `file` (no requiere MongoDB)
- ✅ **Session:** `file` (no requiere MongoDB)
- ✅ **Queue:** `sync` (no requiere MongoDB)
- ✅ **Database:** `mongodb` (solo usa `usuario` y `recuperar-password`)

**No se necesitan migraciones** - Las colecciones se crean automáticamente cuando se insertan los primeros documentos.

---

## 📚 Referencias

- Modelo User: `app/Models/User.php`
- Controlador de Preguntas: `app/Http/Controllers/Api/SecretQuestionController.php`
- Configuración de Cache: `config/cache.php`
- Configuración de Session: `config/session.php`
- Configuración de Queue: `config/queue.php`
- Configuración de Database: `config/database.php`

---

## 🔄 Flujo de la Aplicación

### Registro de Usuario
- **Endpoint:** `POST /api/register`
- **Controlador:** `App\Http\Controllers\Api\RegisterController`
- **Acción:** `App\Actions\Fortify\CreateNewUser`
- **Colección:** `usuario` ✅
- **Campos guardados:** `name`, `email`, `password`, `pregunta_secreta` (como JSON)

### Login de Usuario
- **Endpoint:** `POST /api/login`
- **Controlador:** `App\Http\Controllers\Api\AuthController`
- **Colección:** `usuario` ✅
- **Operación:** Busca usuario por `email` y verifica `password`

### Login con Google
- **Endpoint:** `GET /api/auth/google/callback`
- **Controlador:** `App\Http\Controllers\Api\GoogleAuthController`
- **Colección:** `usuario` ✅
- **Operación:** Crea o actualiza usuario con `google_id`

### Login con Facebook
- **Endpoint:** `GET /api/auth/facebook/callback`
- **Controlador:** `App\Http\Controllers\Api\FacebookAuthController`
- **Colección:** `usuario` ✅
- **Operación:** Crea o actualiza usuario con `facebook_id`

### Recuperar Contraseña
- **Endpoints:** 
  - `POST /api/password/verify-email` - Verifica email y devuelve pregunta secreta
  - `POST /api/password/verify-answer` - Verifica respuesta secreta
  - `POST /api/password/update` - Actualiza contraseña
- **Controlador:** `App\Http\Controllers\Api\PasswordRecoveryController`
- **Colección:** `usuario` ✅ (lee `pregunta_secreta` del usuario)
- **Colección:** `recuperar-password` ✅ (solo para listar preguntas disponibles)

---

## ✅ Resumen

- ✅ Solo 2 colecciones necesarias: `usuario` y `recuperar-password`
- ✅ Todas las operaciones de usuarios (registro, login, OAuth, recuperar contraseña) usan la colección `usuario`
- ✅ La colección `recuperar-password` solo se usa para listar preguntas disponibles
- ✅ Cache, Session y Queue NO usan MongoDB
- ✅ No se necesitan migraciones
- ✅ Las colecciones se crean automáticamente
- ✅ Todo está configurado para funcionar solo con estas 2 colecciones

