<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SendGridService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PasswordRecoveryController extends Controller
{
    protected $sendGridService;

    public function __construct(SendGridService $sendGridService)
    {
        $this->sendGridService = $sendGridService;
    }
    /**
     * Verify email and return secret question or send OTP.
     * Ahora soporta dos métodos: pregunta secreta o OTP por email.
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'method' => 'nullable|string|in:pregunta,otp',
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
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        // Verificar que no sea usuario Google
        if ($user->google_id) {
            return response()->json([
                'errors' => ['email' => ['Esta cuenta fue registrada con Google. Usa la opción de inicio de sesión con Google.']],
                'message' => 'Cuenta de Google.',
            ], 422);
        }

        $method = $request->input('method', 'pregunta'); // Por defecto pregunta secreta

        // Si el método es OTP, enviar código
        if ($method === 'otp') {
            // Generar código OTP (6 dígitos) - Asegurar que sea string y sin espacios
            $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $otpCode = trim((string) $otpCode); // Limpiar espacios y asegurar string
            
            // Verificar que tenga 6 dígitos
            if (strlen($otpCode) !== 6) {
                return response()->json([
                    'errors' => ['email' => ['Error al generar código OTP.']],
                    'message' => 'Error al generar código.',
                ], 500);
            }
            
            $user->otp_code = $otpCode;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();
            
            Log::info("Código OTP de recuperación generado", [
                'email' => $user->email,
                'otp_code' => $otpCode,
                'otp_code_length' => strlen($otpCode),
                'otp_code_type' => gettype($otpCode)
            ]);

            try {
                $this->sendGridService->sendPasswordRecoveryOTP($user->email, $otpCode);
                Log::info("Código de recuperación enviado a: {$user->email}");

                return response()->json([
                    'message' => 'Código enviado al correo. Expira en 10 minutos.',
                    'method' => 'otp',
                ]);
            } catch (\Exception $e) {
                Log::error('Error enviando correo de recuperación: ' . $e->getMessage());
                return response()->json([
                    'errors' => ['email' => ['No se pudo enviar el correo. Intenta de nuevo más tarde.']],
                    'message' => 'Error al enviar correo.',
                ], 500);
            }
        }

        // Método por defecto: pregunta secreta
        // Verificar si el usuario tiene pregunta secreta configurada
        $preguntaSecretaAttr = $user->getAttribute('pregunta_secreta');
        
        if (!$preguntaSecretaAttr) {
            return response()->json([
                'errors' => ['email' => ['Este usuario no tiene una pregunta secreta configurada.']],
                'message' => 'Usuario sin pregunta secreta.',
            ], 404);
        }

        // Obtener pregunta_secreta como array (decodifica JSON automáticamente)
        $preguntaSecreta = $user->getPreguntaSecretaArray();
        
        if (!$preguntaSecreta || !isset($preguntaSecreta['pregunta'])) {
            return response()->json([
                'errors' => ['email' => ['Este usuario no tiene una pregunta secreta configurada correctamente.']],
                'message' => 'Pregunta secreta inválida.',
            ], 404);
        }

        return response()->json([
            'email' => $user->email,
            'pregunta_secreta' => $preguntaSecreta['pregunta'],
            'method' => 'pregunta',
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
     * Ahora soporta dos métodos: pregunta secreta o OTP.
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'new_password' => 'required|string|min:8',
            'new_password_confirmation' => 'required|string|min:8|same:new_password',
            'method' => 'nullable|string|in:pregunta,otp',
            'respuesta_secreta' => 'required_if:method,pregunta|string',
            'otp_code' => 'required_if:method,otp|string|size:6',
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

        $method = $request->input('method', 'pregunta'); // Por defecto pregunta secreta

        // Si el método es OTP, verificar código
        if ($method === 'otp') {
            // Verificar si hay código OTP
            if (!$user->otp_code) {
                return response()->json([
                    'errors' => ['otp_code' => ['No hay código activo. Solicita uno nuevo.']],
                ], 400);
            }

            // Limpiar el código ingresado
            $inputCode = trim((string) $request->otp_code);
            
            // Verificar si el código ha expirado (10 minutos)
            if ($user->otp_expires_at && Carbon::parse($user->otp_expires_at)->isPast()) {
                // Limpiar código expirado
                $user->otp_code = null;
                $user->otp_expires_at = null;
                $user->save();

                return response()->json([
                    'errors' => ['otp_code' => ['Código expirado. El código OTP solo es válido por 10 minutos. Solicita uno nuevo.']],
                ], 400);
            }

            // Obtener y limpiar el código guardado
            $storedCode = trim((string) $user->otp_code);
            
            // Verificar el código - Comparación estricta con strings limpios
            if ($storedCode !== $inputCode) {
                Log::error('Código OTP no coincide en updatePassword', [
                    'email' => $request->email,
                    'stored_code' => $storedCode,
                    'stored_code_length' => strlen($storedCode),
                    'input_code' => $inputCode,
                    'input_code_length' => strlen($inputCode),
                    'stored_type' => gettype($user->otp_code),
                    'input_type' => gettype($inputCode)
                ]);
                
                return response()->json([
                    'errors' => ['otp_code' => ['Código incorrecto. Verifica el código e intenta nuevamente.']],
                ], 400);
            }
        } else {
            // Método por defecto: pregunta secreta
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
        }

        // Actualizar contraseña
        $user->password = Hash::make($request->new_password);
        
        // Limpiar código OTP después de cambiar contraseña
        $user->otp_code = null;
        $user->otp_expires_at = null;
        
        $user->save();

        Log::info("Contraseña actualizada para: {$user->email}");

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente.',
        ]);
    }
}

