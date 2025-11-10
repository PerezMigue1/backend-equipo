<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // Validación ya se hace en RegisterController, pero la mantenemos aquí por seguridad
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('usuario', 'email'), // Especificar tabla de MongoDB
            ],
            'password' => $this->passwordRules(),
            'pregunta_secreta' => ['required', 'string'],
            'respuesta_secreta' => ['required', 'string'],
        ])->validate();

        // Crear usuario con pregunta_secreta como array (el mutator lo convertirá a JSON)
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'pregunta_secreta' => [
                'pregunta' => $input['pregunta_secreta'],
                'respuesta' => $input['respuesta_secreta']
            ],
        ]);

        return $user;
    }
}
