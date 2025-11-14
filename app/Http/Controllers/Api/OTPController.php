<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SendGridService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OTPController extends Controller
{
    protected $sendGridService;

    public function __construct(SendGridService $sendGridService)
    {
        $this->sendGridService = $sendGridService;
    }

    /**
     * Verificar código OTP para activación de cuenta
     */
    public function verifyActivationOTP(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'code' => ['required', 'string', 'size:6'],
            ], [
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico debe ser válido.',
                'code.required' => 'El código OTP es obligatorio.',
                'code.size' => 'El código OTP debe tener 6 dígitos.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no encontrado.',
                ], 404);
            }

            // Verificar si hay código OTP
            if (!$user->otp_code) {
                return response()->json([
                    'message' => 'No hay código activo. Solicita uno nuevo.',
                ], 400);
            }

            // Verificar si el código ha expirado (10 minutos)
            if ($user->otp_expires_at && Carbon::parse($user->otp_expires_at)->isPast()) {
                // Limpiar código expirado
                $user->otp_code = null;
                $user->otp_expires_at = null;
                $user->save();

                return response()->json([
                    'message' => 'Código expirado. El código OTP solo es válido por 10 minutos. Solicita uno nuevo.',
                ], 400);
            }

            // Verificar el código
            if ($user->otp_code !== $request->code) {
                return response()->json([
                    'message' => 'Código incorrecto. Verifica el código e intenta nuevamente.',
                ], 400);
            }

            // Código correcto - activar cuenta
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->email_verified_at = now();
            $user->save();

            Log::info("Cuenta activada para: {$user->email}");

            // Crear token JWT después de activar la cuenta
            $token = auth('api')->login($user);

            // Preparar datos del usuario
            $userData = [
                'id' => (string) $user->_id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at->toDateTimeString(),
            ];

            return response()->json([
                'message' => 'Código verificado correctamente. Cuenta activada.',
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => $userData,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error en verifyActivationOTP: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al verificar el código.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Reenviar código OTP para activación
     */
    public function resendActivationOTP(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'email' => ['required', 'email'],
            ], [
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico debe ser válido.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no encontrado.',
                ], 404);
            }

            // Verificar si la cuenta ya está activada
            if ($user->email_verified_at) {
                return response()->json([
                    'message' => 'Esta cuenta ya está activada.',
                ], 400);
            }

            // Generar nuevo código OTP (6 dígitos)
            $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->otp_code = $otpCode;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();

            // Enviar email
            try {
                $this->sendGridService->sendActivationOTP($user->email, $otpCode);
                Log::info("Código de activación reenviado a: {$user->email}");

                return response()->json([
                    'message' => 'Nuevo código enviado al correo. Recuerda que el código expira en 10 minutos.',
                ], 200);
            } catch (\Exception $e) {
                Log::error('Error enviando correo de activación: ' . $e->getMessage());
                return response()->json([
                    'message' => 'No se pudo enviar el correo. Intenta de nuevo más tarde.',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error en resendActivationOTP: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al reenviar el código.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Verificar código OTP para recuperación de contraseña
     */
    public function verifyPasswordRecoveryOTP(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'code' => ['required', 'string', 'size:6'],
            ], [
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico debe ser válido.',
                'code.required' => 'El código OTP es obligatorio.',
                'code.size' => 'El código OTP debe tener 6 dígitos.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no encontrado.',
                ], 404);
            }

            // Verificar que no sea usuario Google
            if ($user->google_id) {
                return response()->json([
                    'message' => 'Esta cuenta fue registrada con Google. Usa la opción de inicio de sesión con Google.',
                ], 422);
            }

            // Verificar si hay código OTP
            if (!$user->otp_code) {
                return response()->json([
                    'message' => 'No hay código activo. Solicita uno nuevo.',
                ], 400);
            }

            // Verificar si el código ha expirado (10 minutos)
            if ($user->otp_expires_at && Carbon::parse($user->otp_expires_at)->isPast()) {
                // Limpiar código expirado
                $user->otp_code = null;
                $user->otp_expires_at = null;
                $user->save();

                return response()->json([
                    'message' => 'Código expirado. El código OTP solo es válido por 10 minutos. Solicita uno nuevo.',
                ], 400);
            }

            // Verificar el código
            if ($user->otp_code !== $request->code) {
                return response()->json([
                    'message' => 'Código incorrecto. Verifica el código e intenta nuevamente.',
                ], 400);
            }

            // Código correcto - NO limpiar aún, esperar cambio de contraseña
            return response()->json([
                'message' => 'Código verificado correctamente. Ahora puedes cambiar tu contraseña.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error en verifyPasswordRecoveryOTP: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al verificar el código.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Reenviar código OTP para recuperación de contraseña
     */
    public function resendPasswordRecoveryOTP(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'email' => ['required', 'email'],
            ], [
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico debe ser válido.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no encontrado.',
                ], 404);
            }

            // Verificar que no sea usuario Google
            if ($user->google_id) {
                return response()->json([
                    'message' => 'Esta cuenta fue registrada con Google. Usa la opción de inicio de sesión con Google.',
                ], 422);
            }

            // Generar nuevo código OTP (6 dígitos)
            $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->otp_code = $otpCode;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();

            // Enviar email
            try {
                $this->sendGridService->sendPasswordRecoveryOTP($user->email, $otpCode);
                Log::info("Código de recuperación reenviado a: {$user->email}");

                return response()->json([
                    'message' => 'Nuevo código enviado al correo. Expira en 10 minutos.',
                ], 200);
            } catch (\Exception $e) {
                Log::error('Error enviando correo de recuperación: ' . $e->getMessage());
                return response()->json([
                    'message' => 'No se pudo enviar el correo. Intenta de nuevo más tarde.',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error en resendPasswordRecoveryOTP: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al reenviar el código.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}

