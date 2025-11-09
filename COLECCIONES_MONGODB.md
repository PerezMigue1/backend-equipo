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
- `pregunta_secreta` (array: `pregunta`, `respuesta`)
- `respuesta_secreta` (string)
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
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "$2y$10$...",
  "pregunta_secreta": {
    "pregunta": "¿Cuál es el nombre de tu mascota?",
    "respuesta": "Fido"
  },
  "google_id": "123456789",
  "facebook_id": null,
  "created_at": ISODate("2024-01-01T00:00:00Z"),
  "updated_at": ISODate("2024-01-01T00:00:00Z")
}
```

### Colección `recuperar-password`
```json
{
  "_id": ObjectId("..."),
  "pregunta": "¿Cuál es el nombre de tu mascota?",
  "activo": true
}
```

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

## ✅ Resumen

- ✅ Solo 2 colecciones necesarias: `usuario` y `recuperar-password`
- ✅ Cache, Session y Queue NO usan MongoDB
- ✅ No se necesitan migraciones
- ✅ Las colecciones se crean automáticamente
- ✅ Todo está configurado para funcionar solo con estas 2 colecciones

