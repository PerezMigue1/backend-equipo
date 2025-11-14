<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico debe ser válido.',
                'password.required' => 'La contraseña es obligatoria.',
            ]);

            \Log::info('Login attempt for email: ' . $request->email);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                \Log::warning('User not found: ' . $request->email);
                throw ValidationException::withMessages([
                    'email' => ['No se encontró un usuario con este correo electrónico.'],
                ]);
            }

            if (!Hash::check($request->password, $user->password)) {
                \Log::warning('Invalid password for user: ' . $request->email);
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales proporcionadas son incorrectas.'],
                ]);
            }

            \Log::info('User authenticated successfully: ' . $user->email);

            // Crear token JWT
            try {
                $token = auth('api')->login($user);
                \Log::info('Token JWT created successfully for user: ' . $user->email);
            } catch (\Exception $e) {
                \Log::error('Error creating JWT token: ' . $e->getMessage());
                \Log::error('Token error trace: ' . $e->getTraceAsString());
                throw new \Exception('Error al crear el token de autenticación: ' . $e->getMessage());
            }

            // Preparar datos del usuario para la respuesta
            // Obtener atributos directamente sin acceder a pregunta_secreta
            $userData = [
                '_id' => (string) $user->_id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : null,
                'google_id' => $user->google_id ?? null,
                'facebook_id' => $user->facebook_id ?? null,
                'created_at' => $user->created_at ? $user->created_at->toDateTimeString() : null,
                'updated_at' => $user->updated_at ? $user->updated_at->toDateTimeString() : null,
            ];

            return response()->json([
                'user' => $userData,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'message' => 'Login exitoso',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Error de autenticación.',
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error en login: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'Error al iniciar sesión. Por favor, intenta de nuevo.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
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
                    'message' => 'Usuario no autenticado.',
                ], 401);
            }
            
            // Preparar datos del usuario para la respuesta
            // Obtener atributos directamente sin acceder a pregunta_secreta
            $userData = [
                '_id' => (string) $user->_id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : null,
                'google_id' => $user->google_id ?? null,
                'facebook_id' => $user->facebook_id ?? null,
                'created_at' => $user->created_at ? $user->created_at->toDateTimeString() : null,
                'updated_at' => $user->updated_at ? $user->updated_at->toDateTimeString() : null,
            ];
            
            return response()->json($userData);
        } catch (\Exception $e) {
            \Log::error('Error al obtener usuario: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al obtener información del usuario.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        auth('api')->logout();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
