<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback and create/update user.
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            if (!$googleUser->getEmail()) {
                throw new \Exception('No se pudo obtener el email de Google');
            }

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName() ?: 'Usuario Google',
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                    'google_id' => $googleUser->getId(),
                ]);
            } else {
                // Actualizar google_id si no existe
                if (!$user->google_id) {
                    $user->google_id = $googleUser->getId();
                    $user->save();
                }
            }

            $token = $user->createToken('auth-token')->plainTextToken;

            // Redirect to frontend with token
            // Usar config() en lugar de env() para mejor rendimiento y cache
            $frontendUrl = config('app.frontend_url', 'https://modulo-usuario.netlify.app');
            return redirect($frontendUrl . '/auth/callback?token=' . $token . '&provider=google');
        } catch (\Exception $e) {
            \Log::error('Error en Google OAuth: ' . $e->getMessage());
            // Usar config() en lugar de env() para mejor rendimiento y cache
            $frontendUrl = config('app.frontend_url', 'https://modulo-usuario.netlify.app');
            $errorMessage = config('app.debug') 
                ? 'Error al autenticar con Google: ' . $e->getMessage()
                : 'Error al autenticar con Google. Por favor, intenta de nuevo.';
            return redirect($frontendUrl . '/login?error=' . urlencode($errorMessage));
        }
    }
}

