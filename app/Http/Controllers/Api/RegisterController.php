<?php

namespace App\Http\Controllers\Api;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SendGridService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    protected $sendGridService;

    public function __construct(SendGridService $sendGridService)
    {
        $this->sendGridService = $sendGridService;
    }

    /**
     * Handle registration request.
     * Ahora envía OTP en lugar de crear token inmediatamente.
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

            // Generar código OTP (6 dígitos) - Asegurar que sea string y sin espacios
            $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $otpCode = trim((string) $otpCode); // Limpiar espacios y asegurar string
            
            // Verificar que tenga 6 dígitos
            if (strlen($otpCode) !== 6) {
                throw new \Exception('Error al generar código OTP: longitud incorrecta');
            }
            
            $user->otp_code = $otpCode;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->email_verified_at = null; // No verificado hasta que se valide el OTP
            $user->save();

            Log::info("Usuario registrado: {$user->email}, OTP: {$otpCode}, Expira en 10 minutos", [
                'otp_code' => $otpCode,
                'otp_code_length' => strlen($otpCode),
                'otp_code_type' => gettype($otpCode)
            ]);

            // Enviar email con OTP
            try {
                $this->sendGridService->sendActivationOTP($user->email, $otpCode);
                
                return response()->json([
                    'message' => 'Registro exitoso. Ingresa el código enviado a tu correo para activar tu cuenta. El código expira en 10 minutos.',
                    'email' => $user->email,
                ], 201);
            } catch (\Exception $e) {
                Log::error('Error al enviar correo de activación: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Usuario registrado, pero no se pudo enviar el correo de activación. Por favor, solicita un nuevo código.',
                    'email' => $user->email,
                ], 201);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Error de validación.',
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en registro: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al registrar usuario. Por favor, intenta de nuevo.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
