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
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'pregunta_secreta' => ['required', 'string'],
            'respuesta_secreta' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $creator->create($request->all());

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->makeHidden(['password', 'two_factor_secret', 'two_factor_recovery_codes']),
            'token' => $token,
            'message' => 'Registration successful',
        ], 201);
    }
}

