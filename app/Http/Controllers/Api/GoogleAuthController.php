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
     * Después del login, redirige directamente al home.
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName() ?: ($googleUser->user["name"] ?? 'Usuario Google'),
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                ]);
            } else if (!$user->google_id) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }

            // Crear token JWT
            $token = auth('api')->login($user);

            // Redirigir directamente al home (no a /auth/callback)
            $frontendUrl = config('app.frontend_url', 'https://modulo-usuario.netlify.app');
            return redirect($frontendUrl);
        } catch (\Exception $e) {
            \Log::error('Error en Google OAuth: ' . $e->getMessage());
            $frontendUrl = config('app.frontend_url', 'https://modulo-usuario.netlify.app');
            $errorMessage = config('app.debug') 
                ? 'Error al autenticar con Google: ' . $e->getMessage()
                : 'Error al autenticar con Google. Intenta de nuevo.';
            return redirect($frontendUrl . '/login?error=' . urlencode($errorMessage));
        }
    }
}

