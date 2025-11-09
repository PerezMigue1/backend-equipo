<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordRecoveryController extends Controller
{
    /**
     * Verify email and return secret question.
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'errors' => ['email' => ['No se encontró un usuario con ese correo electrónico.']],
            ], 404);
        }

        // Obtener pregunta_secreta como array (decodifica JSON automáticamente)
        $preguntaSecreta = $user->getPreguntaSecretaArray();
        
        if (!$preguntaSecreta || !isset($preguntaSecreta['pregunta'])) {
            return response()->json([
                'errors' => ['email' => ['Este usuario no tiene una pregunta secreta configurada.']],
            ], 404);
        }

        return response()->json([
            'email' => $user->email,
            'pregunta_secreta' => $preguntaSecreta['pregunta'],
        ]);
    }

    /**
     * Verify secret answer.
     */
    public function verifyAnswer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'respuesta_secreta' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'errors' => ['email' => ['Usuario no encontrado.']],
            ], 404);
        }

        // Obtener pregunta_secreta como array (decodifica JSON automáticamente)
        $preguntaSecreta = $user->getPreguntaSecretaArray();
        
        $respuestaCorrecta = $preguntaSecreta && 
                            isset($preguntaSecreta['respuesta']) &&
                            strtolower($preguntaSecreta['respuesta']) === strtolower($request->respuesta_secreta);

        if (!$respuestaCorrecta) {
            return response()->json([
                'errors' => ['respuesta_secreta' => ['La respuesta secreta no es correcta.']],
            ], 422);
        }

        return response()->json([
            'message' => 'Respuesta correcta. Puede proceder a cambiar la contraseña.',
            'verified' => true,
        ]);
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'new_password' => 'required|string|min:8',
            'new_password_confirmation' => 'required|string|min:8|same:new_password',
            'respuesta_secreta' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'errors' => ['email' => ['Usuario no encontrado.']],
            ], 404);
        }

        // Obtener pregunta_secreta como array (decodifica JSON automáticamente)
        $preguntaSecreta = $user->getPreguntaSecretaArray();
        
        // Verificar respuesta secreta antes de actualizar
        if (!$preguntaSecreta || 
            !isset($preguntaSecreta['respuesta']) ||
            strtolower($preguntaSecreta['respuesta']) !== strtolower($request->respuesta_secreta)) {
            return response()->json([
                'errors' => ['respuesta_secreta' => ['La respuesta secreta no es correcta.']],
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente.',
        ]);
    }
}

