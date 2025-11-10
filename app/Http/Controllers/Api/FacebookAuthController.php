<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class FacebookAuthController extends Controller
{
    /**
     * Redirect to Facebook authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Handle Facebook callback and create/update user.
     */
    public function callback(Request $request)
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            $user = User::where('email', $facebookUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $facebookUser->getName() ?: ($facebookUser->user["name"] ?? 'Usuario Facebook'),
                    'email' => $facebookUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                    'facebook_id' => $facebookUser->getId(),
                    'email_verified_at' => now(),
                ]);
            } else if (!$user->facebook_id) {
                $user->facebook_id = $facebookUser->getId();
                $user->save();
            }

            // Crear token JWT
            $token = auth('api')->login($user);

            // Redirect to frontend with token
            // Usar config() en lugar de env() para mejor rendimiento y cache
            $frontendUrl = config('app.frontend_url', 'https://modulo-usuario.netlify.app');
            return redirect($frontendUrl . '/auth/callback?token=' . $token . '&provider=facebook');
        } catch (\Exception $e) {
            \Log::error('Error en Facebook OAuth: ' . $e->getMessage());
            // Usar config() en lugar de env() para mejor rendimiento y cache
            $frontendUrl = config('app.frontend_url', 'https://modulo-usuario.netlify.app');
            $errorMessage = config('app.debug') 
                ? 'Error al autenticar con Facebook: ' . $e->getMessage()
                : 'Error al autenticar con Facebook. Intenta de nuevo.';
            return redirect($frontendUrl . '/login?error=' . urlencode($errorMessage));
        }
    }
}

