<?php

namespace App\Http\Controllers\Api;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /**
     * Handle registration request.
     */
    public function store(Request $request, CreateNewUser $creator)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('usuario', 'email'), // Especificar tabla de MongoDB
                ],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'pregunta_secreta' => ['required', 'string'],
                'respuesta_secreta' => ['required', 'string'],
            ], [
                'name.required' => 'El nombre es obligatorio.',
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico debe ser válido.',
                'email.unique' => 'Este correo electrónico ya está registrado.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
                'pregunta_secreta.required' => 'La pregunta secreta es obligatoria.',
                'respuesta_secreta.required' => 'La respuesta secreta es obligatoria.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Error de validación. Por favor, verifica los datos ingresados.',
                ], 422);
            }

            $user = $creator->create($request->all());

            // Ocultar campos sensibles antes de devolver
            $userData = $user->makeHidden([
                'password', 
                'two_factor_secret', 
                'two_factor_recovery_codes',
                'remember_token',
                'pregunta_secreta' // No devolver la respuesta secreta
            ])->toArray();

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'user' => $userData,
                'token' => $token,
                'message' => 'Registro exitoso',
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Error de validación.',
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error en registro: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al registrar usuario. Por favor, intenta de nuevo.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}