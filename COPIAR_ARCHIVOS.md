# Instrucciones para Copiar Archivos al Backend

Este documento lista todos los archivos que debes copiar desde el proyecto actual al backend.

## Archivos a Copiar

### 1. Controllers API
```
app/Http/Controllers/Api/AuthController.php → backend/app/Http/Controllers/Api/
app/Http/Controllers/Api/RegisterController.php → backend/app/Http/Controllers/Api/
app/Http/Controllers/Api/PasswordRecoveryController.php → backend/app/Http/Controllers/Api/
app/Http/Controllers/Api/SecretQuestionController.php → backend/app/Http/Controllers/Api/
app/Http/Controllers/Api/GoogleAuthController.php → backend/app/Http/Controllers/Api/
app/Http/Controllers/Api/FacebookAuthController.php → backend/app/Http/Controllers/Api/
app/Http/Controllers/Controller.php → backend/app/Http/Controllers/
```

### 2. Models
```
app/Models/User.php → backend/app/Models/
```

### 3. Actions (Fortify)
```
app/Actions/Fortify/CreateNewUser.php → backend/app/Actions/Fortify/
app/Actions/Fortify/ResetUserPassword.php → backend/app/Actions/Fortify/
app/Actions/Fortify/PasswordValidationRules.php → backend/app/Actions/Fortify/
```

### 4. Providers
```
app/Providers/AppServiceProvider.php → backend/app/Providers/
app/Providers/FortifyServiceProvider.php → backend/app/Providers/
```

### 5. Routes
```
routes/api.php → backend/routes/
routes/web.php → backend/routes/ (solo rutas OAuth)
routes/console.php → backend/routes/
```

### 6. Config
```
config/sanctum.php → backend/config/
config/cors.php → backend/config/
config/database.php → backend/config/
config/services.php → backend/config/
config/auth.php → backend/config/
config/fortify.php → backend/config/
config/app.php → backend/config/
config/cache.php → backend/config/
config/filesystems.php → backend/config/
config/logging.php → backend/config/
config/mail.php → backend/config/
config/queue.php → backend/config/
config/session.php → backend/config/
```

### 7. Bootstrap
```
bootstrap/app.php → backend/bootstrap/
bootstrap/providers.php → backend/bootstrap/
```

### 8. Database
```
database/migrations/ → backend/database/migrations/
database/seeders/ → backend/database/seeders/
database/factories/ → backend/database/factories/
```

### 9. Public
```
public/index.php → backend/public/
public/.htaccess → backend/public/ (si existe)
```

### 10. Otros
```
artisan → backend/
phpunit.xml → backend/
.gitignore → backend/
.env.example → backend/
```

### 11. Para Render.com
```
Dockerfile → backend/
render.yaml → backend/
start.sh → backend/
```

## Comandos para Copiar (Linux/Mac)

```bash
# Crear estructura de directorios
mkdir -p backend/app/Http/Controllers/Api
mkdir -p backend/app/Models
mkdir -p backend/app/Actions/Fortify
mkdir -p backend/app/Providers
mkdir -p backend/routes
mkdir -p backend/config
mkdir -p backend/bootstrap
mkdir -p backend/database/migrations
mkdir -p backend/database/seeders
mkdir -p backend/database/factories
mkdir -p backend/public

# Copiar archivos
cp app/Http/Controllers/Api/*.php backend/app/Http/Controllers/Api/
cp app/Http/Controllers/Controller.php backend/app/Http/Controllers/
cp app/Models/User.php backend/app/Models/
cp app/Actions/Fortify/*.php backend/app/Actions/Fortify/
cp app/Providers/*.php backend/app/Providers/
cp routes/api.php backend/routes/
cp routes/console.php backend/routes/
cp config/sanctum.php backend/config/
cp config/cors.php backend/config/
cp config/database.php backend/config/
cp config/services.php backend/config/
cp config/auth.php backend/config/
cp config/fortify.php backend/config/
cp config/app.php backend/config/
cp config/cache.php backend/config/
cp config/filesystems.php backend/config/
cp config/logging.php backend/config/
cp config/mail.php backend/config/
cp config/queue.php backend/config/
cp config/session.php backend/config/
cp bootstrap/app.php backend/bootstrap/
cp bootstrap/providers.php backend/bootstrap/
cp artisan backend/
cp phpunit.xml backend/
cp .gitignore backend/
cp .env.example backend/
cp Dockerfile backend/
cp render.yaml backend/
cp start.sh backend/
cp public/index.php backend/public/
```

## Comandos para Copiar (Windows PowerShell)

```powershell
# Crear estructura de directorios
New-Item -ItemType Directory -Force -Path backend/app/Http/Controllers/Api
New-Item -ItemType Directory -Force -Path backend/app/Models
New-Item -ItemType Directory -Force -Path backend/app/Actions/Fortify
New-Item -ItemType Directory -Force -Path backend/app/Providers
New-Item -ItemType Directory -Force -Path backend/routes
New-Item -ItemType Directory -Force -Path backend/config
New-Item -ItemType Directory -Force -Path backend/bootstrap
New-Item -ItemType Directory -Force -Path backend/database/migrations
New-Item -ItemType Directory -Force -Path backend/database/seeders
New-Item -ItemType Directory -Force -Path backend/database/factories
New-Item -ItemType Directory -Force -Path backend/public

# Copiar archivos
Copy-Item app/Http/Controllers/Api/*.php backend/app/Http/Controllers/Api/
Copy-Item app/Http/Controllers/Controller.php backend/app/Http/Controllers/
Copy-Item app/Models/User.php backend/app/Models/
Copy-Item app/Actions/Fortify/*.php backend/app/Actions/Fortify/
Copy-Item app/Providers/*.php backend/app/Providers/
Copy-Item routes/api.php backend/routes/
Copy-Item routes/console.php backend/routes/
Copy-Item config/sanctum.php backend/config/
Copy-Item config/cors.php backend/config/
Copy-Item config/database.php backend/config/
Copy-Item config/services.php backend/config/
Copy-Item config/auth.php backend/config/
Copy-Item config/fortify.php backend/config/
Copy-Item config/app.php backend/config/
Copy-Item config/cache.php backend/config/
Copy-Item config/filesystems.php backend/config/
Copy-Item config/logging.php backend/config/
Copy-Item config/mail.php backend/config/
Copy-Item config/queue.php backend/config/
Copy-Item config/session.php backend/config/
Copy-Item bootstrap/app.php backend/bootstrap/
Copy-Item bootstrap/providers.php backend/bootstrap/
Copy-Item artisan backend/
Copy-Item phpunit.xml backend/
Copy-Item .gitignore backend/
Copy-Item .env.example backend/
Copy-Item Dockerfile backend/
Copy-Item render.yaml backend/
Copy-Item start.sh backend/
Copy-Item public/index.php backend/public/
```

## Notas Importantes

1. **routes/web.php**: Solo copia las rutas OAuth (auth/google, auth/facebook). Elimina las rutas de vistas.

2. **bootstrap/app.php**: Asegúrate de que tenga la configuración de API y CORS.

3. **.env.example**: Actualiza con las variables necesarias para el backend.

4. **Dockerfile, render.yaml, start.sh**: Estos archivos ya están configurados para el backend.

## Después de Copiar

1. En el backend, ejecuta:
   ```bash
   composer install
   php artisan key:generate
   ```

2. Configura `.env` con las variables correctas.

3. Prueba que el backend funcione:
   ```bash
   php artisan serve
   ```

