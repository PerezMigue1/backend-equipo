# Script para copiar archivos del backend
# Ejecutar desde la raíz del proyecto

Write-Host "Creando estructura de directorios..." -ForegroundColor Green

# Crear directorios
$directories = @(
    "backend/app/Http/Controllers/Api",
    "backend/app/Http/Controllers",
    "backend/app/Models",
    "backend/app/Actions/Fortify",
    "backend/app/Providers",
    "backend/routes",
    "backend/config",
    "backend/bootstrap",
    "backend/database/migrations",
    "backend/database/seeders",
    "backend/database/factories",
    "backend/public",
    "backend/storage/app/public",
    "backend/storage/framework/cache",
    "backend/storage/framework/sessions",
    "backend/storage/framework/views",
    "backend/storage/logs"
)

foreach ($dir in $directories) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Force -Path $dir | Out-Null
        Write-Host "Creado: $dir" -ForegroundColor Yellow
    }
}

Write-Host "`nCopiando archivos..." -ForegroundColor Green

# Copiar Controllers API
Copy-Item -Path "app/Http/Controllers/Api/*.php" -Destination "backend/app/Http/Controllers/Api/" -Force -ErrorAction SilentlyContinue
Write-Host "✓ Controllers API" -ForegroundColor Cyan

# Copiar Controller base
Copy-Item -Path "app/Http/Controllers/Controller.php" -Destination "backend/app/Http/Controllers/" -Force -ErrorAction SilentlyContinue
Write-Host "✓ Controller base" -ForegroundColor Cyan

# Copiar Models
Copy-Item -Path "app/Models/User.php" -Destination "backend/app/Models/" -Force -ErrorAction SilentlyContinue
Write-Host "✓ Models" -ForegroundColor Cyan

# Copiar Actions
Copy-Item -Path "app/Actions/Fortify/*.php" -Destination "backend/app/Actions/Fortify/" -Force -ErrorAction SilentlyContinue
Write-Host "✓ Actions Fortify" -ForegroundColor Cyan

# Copiar Providers
Copy-Item -Path "app/Providers/*.php" -Destination "backend/app/Providers/" -Force -ErrorAction SilentlyContinue
Write-Host "✓ Providers" -ForegroundColor Cyan

# Copiar Routes
Copy-Item -Path "routes/api.php" -Destination "backend/routes/" -Force -ErrorAction SilentlyContinue
Copy-Item -Path "routes/console.php" -Destination "backend/routes/" -Force -ErrorAction SilentlyContinue
Write-Host "✓ Routes" -ForegroundColor Cyan

# Copiar Config
$configFiles = @(
    "cors.php",
    "database.php",
    "services.php",
    "auth.php",
    "fortify.php",
    "app.php",
    "cache.php",
    "filesystems.php",
    "logging.php",
    "mail.php",
    "queue.php",
    "session.php",
    "jwt.php"
)

foreach ($file in $configFiles) {
    if (Test-Path "config/$file") {
        Copy-Item -Path "config/$file" -Destination "backend/config/" -Force -ErrorAction SilentlyContinue
    }
}
Write-Host "✓ Config files" -ForegroundColor Cyan

# Copiar Bootstrap
Copy-Item -Path "bootstrap/app.php" -Destination "backend/bootstrap/" -Force -ErrorAction SilentlyContinue
if (Test-Path "bootstrap/providers.php") {
    Copy-Item -Path "bootstrap/providers.php" -Destination "backend/bootstrap/" -Force -ErrorAction SilentlyContinue
}
Write-Host "✓ Bootstrap" -ForegroundColor Cyan

# Copiar Database
if (Test-Path "database/migrations") {
    Copy-Item -Path "database/migrations/*.php" -Destination "backend/database/migrations/" -Force -ErrorAction SilentlyContinue
}
if (Test-Path "database/seeders") {
    Copy-Item -Path "database/seeders/*.php" -Destination "backend/database/seeders/" -Force -ErrorAction SilentlyContinue
}
if (Test-Path "database/factories") {
    Copy-Item -Path "database/factories/*.php" -Destination "backend/database/factories/" -Force -ErrorAction SilentlyContinue
}
Write-Host "✓ Database files" -ForegroundColor Cyan

# Copiar Public
Copy-Item -Path "public/index.php" -Destination "backend/public/" -Force -ErrorAction SilentlyContinue
Write-Host "✓ Public files" -ForegroundColor Cyan

# Copiar otros archivos
$otherFiles = @(
    "artisan",
    "phpunit.xml",
    ".gitignore",
    ".env.example",
    "Dockerfile",
    "render.yaml",
    "start.sh"
)

foreach ($file in $otherFiles) {
    if (Test-Path $file) {
        Copy-Item -Path $file -Destination "backend/" -Force -ErrorAction SilentlyContinue
    }
}
Write-Host "✓ Otros archivos" -ForegroundColor Cyan

Write-Host "`n✓ Archivos copiados exitosamente!" -ForegroundColor Green
Write-Host "`nPróximos pasos:" -ForegroundColor Yellow
Write-Host "1. Revisa backend/routes/web.php y deja solo las rutas OAuth" -ForegroundColor White
Write-Host "2. En backend/, ejecuta: composer install" -ForegroundColor White
Write-Host "3. Configura backend/.env con las variables correctas" -ForegroundColor White

