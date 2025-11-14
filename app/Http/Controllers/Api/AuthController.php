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
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no autenticado',
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
        } catch (\Exception $e) {
            Log::error('Error obteniendo usuario: ' . $e->getMessage());
            
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
