<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Credenciales inválidas',
                ], 401);
            }

            // Crear token JWT
            $token = auth('api')->login($user);

            if (!$token) {
                Log::error('Error creating JWT token for user: ' . $user->email);
                return response()->json([
                    'message' => 'Error al crear token de autenticación',
                ], 500);
            }

            // Preparar datos del usuario (sin pregunta_secreta directamente)
            $userData = [
                'id' => (string) $user->_id,
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->google_id ?? null,
                'facebook_id' => $user->facebook_id ?? null,
            ];

            return response()->json([
                'message' => 'Login exitoso',
                'token' => $token,
                'user' => $userData,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error en login: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get authenticated user.
     */
    public function user(Request $request)
    {
        try {
            // Log para debugging
            $token = $request->bearerToken();
            Log::info('Token recibido: ' . ($token ? substr($token, 0, 20) . '...' : 'No hay token'));
            
            // Intentar obtener el usuario
            $user = auth('api')->user();
            
            // Si no hay usuario, intentar obtener el error de JWT
            if (!$user) {
                try {
                    $payload = auth('api')->payload();
                    Log::info('Payload JWT: ' . json_encode($payload->toArray()));
                } catch (\Exception $jwtError) {
                    Log::error('Error JWT al obtener payload: ' . $jwtError->getMessage());
                    Log::error('Tipo de error: ' . get_class($jwtError));
                }
                
                return response()->json([
                    'message' => 'Usuario no autenticado',
                    'debug' => config('app.debug') ? [
                        'has_token' => !empty($token),
                        'jwt_secret_set' => !empty(config('jwt.secret')),
                    ] : null,
                ], 401);
            }

            // Preparar datos del usuario (sin pregunta_secreta directamente)
            $userData = [
                'id' => (string) $user->_id,
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->google_id ?? null,
                'facebook_id' => $user->facebook_id ?? null,
            ];

            return response()->json([
                'user' => $userData,
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            Log::error('Token expirado: ' . $e->getMessage());
            return response()->json([
                'message' => 'Token expirado',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            Log::error('Token inválido: ' . $e->getMessage());
            return response()->json([
                'message' => 'Token inválido',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            Log::error('Error JWT: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error de autenticación',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 401);
        } catch (\Exception $e) {
            Log::error('Error obteniendo usuario: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'message' => 'Error al obtener usuario',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        try {
            auth('api')->logout();

            return response()->json([
                'message' => 'Logout exitoso',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error en logout: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Error al cerrar sesión',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
