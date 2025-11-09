# Script para probar las APIs en Render
# Ejecutar: .\test-api.ps1

$baseUrl = "https://backend-equipo.onrender.com"

Write-Host "=== Verificando APIs en Render ===" -ForegroundColor Green
Write-Host "URL Base: $baseUrl`n" -ForegroundColor Gray

# 1. Health Check
Write-Host "1. Health Check..." -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$baseUrl/up" -Method GET -ErrorAction Stop
    Write-Host "   ✅ Health Check OK" -ForegroundColor Green
    Write-Host "   Respuesta: $($response | ConvertTo-Json -Compress)" -ForegroundColor Gray
} catch {
    Write-Host "   ❌ Health Check Falló" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
}

# 2. Preguntas Secretas
Write-Host "`n2. Preguntas Secretas..." -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$baseUrl/api/preguntas-secretas" -Method GET -ErrorAction Stop
    Write-Host "   ✅ Preguntas Secretas OK" -ForegroundColor Green
    if ($response.preguntas) {
        Write-Host "   Preguntas encontradas: $($response.preguntas.Count)" -ForegroundColor Gray
    } else {
        Write-Host "   Respuesta: $($response | ConvertTo-Json -Compress)" -ForegroundColor Gray
    }
} catch {
    Write-Host "   ❌ Preguntas Secretas Falló" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
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
    $response = Invoke-RestMethod -Uri "$baseUrl/api/register" -Method POST -Body $registerBody -ContentType "application/json" -ErrorAction Stop
    Write-Host "   ✅ Registro OK" -ForegroundColor Green
    Write-Host "   Usuario creado: $testEmail" -ForegroundColor Gray
    $token = $response.token
    if ($token) {
        Write-Host "   Token obtenido: $($token.Substring(0, [Math]::Min(30, $token.Length)))..." -ForegroundColor Gray
        
        # 4. Test de Usuario Actual (con token)
        Write-Host "`n4. Usuario Actual (con token)..." -ForegroundColor Yellow
        $headers = @{
            "Authorization" = "Bearer $token"
            "Accept" = "application/json"
        }
        try {
            $userResponse = Invoke-RestMethod -Uri "$baseUrl/api/user" -Method GET -Headers $headers -ErrorAction Stop
            Write-Host "   ✅ Usuario Actual OK" -ForegroundColor Green
            Write-Host "   Email: $($userResponse.email)" -ForegroundColor Gray
            Write-Host "   Nombre: $($userResponse.name)" -ForegroundColor Gray
        } catch {
            Write-Host "   ❌ Usuario Actual Falló" -ForegroundColor Red
            Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
            if ($_.Exception.Response) {
                try {
                    $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
                    $responseBody = $reader.ReadToEnd()
                    Write-Host "   Respuesta: $responseBody" -ForegroundColor Gray
                } catch {
                    # Ignorar errores al leer la respuesta
                }
            }
        }
    }
} catch {
    Write-Host "   ❌ Registro Falló" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
    if ($_.Exception.Response) {
        try {
            $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
            $responseBody = $reader.ReadToEnd()
            Write-Host "   Respuesta del servidor: $responseBody" -ForegroundColor Gray
        } catch {
            # Ignorar errores al leer la respuesta
        }
    }
}

Write-Host "`n=== Verificación Completa ===" -ForegroundColor Green
Write-Host "`nPara más detalles, revisa los logs en Render Dashboard" -ForegroundColor Gray
Write-Host "URL: https://dashboard.render.com" -ForegroundColor Gray

