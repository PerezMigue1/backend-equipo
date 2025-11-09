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

            $token = $user->createToken('auth-token')->plainTextToken;

            // Redirect to frontend with token
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            return redirect($frontendUrl . '/auth/callback?token=' . $token . '&provider=facebook');
        } catch (\Exception $e) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            return redirect($frontendUrl . '/login?error=' . urlencode('Error al autenticar con Facebook. Intenta de nuevo.'));
        }
    }
}

