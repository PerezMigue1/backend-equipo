<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OTPController;
use App\Http\Controllers\Api\PasswordRecoveryController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SecretQuestionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'store']);
Route::get('/preguntas-secretas', [SecretQuestionController::class, 'index']);

// OTP routes (activación de cuenta)
Route::post('/otp/verify-activation', [OTPController::class, 'verifyActivationOTP']);
Route::post('/otp/resend-activation', [OTPController::class, 'resendActivationOTP']);

// Password recovery routes
Route::post('/password/verify-email', [PasswordRecoveryController::class, 'verifyEmail']);
Route::post('/password/verify-answer', [PasswordRecoveryController::class, 'verifyAnswer']);
Route::post('/password/update', [PasswordRecoveryController::class, 'updatePassword']);

// OTP routes (recuperación de contraseña)
Route::post('/otp/verify-password-recovery', [OTPController::class, 'verifyPasswordRecoveryOTP']);
Route::post('/otp/resend-password-recovery', [OTPController::class, 'resendPasswordRecoveryOTP']);

// Protected routes (require authentication)
// Note: OAuth routes are in web.php because they need session handling
Route::middleware('auth:api')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Endpoint para listar usuarios desde MongoDB (solo lectura)
Route::get('/usuarios/list', function () {
    try {
        // Obtener la URI de MongoDB desde la configuración
        $mongodbUri = config('database.connections.mongodb.dsn') ?: env('MONGODB_URI');
        $databaseName = config('database.connections.mongodb.database') ?: env('MONGODB_DATABASE', 'equipo');

        if (!$mongodbUri) {
            return response()->json([
                'error' => 'MONGODB_URI no está configurada',
                'message' => 'Error de configuración',
            ], 500);
        }

        $client = new MongoDB\Client($mongodbUri);
        $database = $client->selectDatabase($databaseName);
        $collection = $database->selectCollection('usuario');

        $cursor = $collection->find();
        $usuarios = [];

        foreach ($cursor as $document) {
            // Convertir documento MongoDB a array (igual que en SecretQuestionController)
            $docArray = iterator_to_array($document);
            
            // Decodificar pregunta_secreta si es string JSON
            $preguntaSecreta = null;
            if (isset($docArray['pregunta_secreta'])) {
                if (is_string($docArray['pregunta_secreta'])) {
                    $preguntaSecreta = json_decode($docArray['pregunta_secreta'], true);
                } else {
                    $preguntaSecreta = $docArray['pregunta_secreta'];
                }
            }
            
            // Manejar fechas (pueden ser MongoDB\BSON\UTCDateTime o strings)
            $createdAt = null;
            if (isset($docArray['created_at'])) {
                if ($docArray['created_at'] instanceof MongoDB\BSON\UTCDateTime) {
                    $createdAt = $docArray['created_at']->toDateTime()->format('Y-m-d H:i:s');
                } elseif (is_string($docArray['created_at'])) {
                    $createdAt = $docArray['created_at'];
                }
            }
            
            $updatedAt = null;
            if (isset($docArray['updated_at'])) {
                if ($docArray['updated_at'] instanceof MongoDB\BSON\UTCDateTime) {
                    $updatedAt = $docArray['updated_at']->toDateTime()->format('Y-m-d H:i:s');
                } elseif (is_string($docArray['updated_at'])) {
                    $updatedAt = $docArray['updated_at'];
                }
            }
            
            $usuario = [
                '_id' => (string) $docArray['_id'],
                'name' => $docArray['name'] ?? null,
                'email' => $docArray['email'] ?? null,
                'pregunta_secreta' => $preguntaSecreta,
                'facebook_id' => $docArray['facebook_id'] ?? null,
                'remember_token' => isset($docArray['remember_token']) ? substr($docArray['remember_token'], 0, 20) . '...' : null,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];
            
            $usuarios[] = $usuario;
        }

        return response()->json([
            'total' => count($usuarios),
            'coleccion' => 'usuario',
            'base_datos' => $databaseName,
            'usuarios' => $usuarios,
        ], 200);
    } catch (\MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
        return response()->json([
            'error' => 'Error de conexión a MongoDB',
            'message' => 'No se pudo conectar a la base de datos',
            'detalle' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'message' => 'Error al consultar la colección usuario',
            'detalle' => config('app.debug') ? $e->getTraceAsString() : null,
        ], 500);
    }
});

