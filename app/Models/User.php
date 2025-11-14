<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, TwoFactorAuthenticatable;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * The collection name for the model.
     *
     * @var string
     */
    protected $collection = 'usuario';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = '_id';

    /**
     * Set the table name to prevent Laravel from pluralizing it.
     *
     * @var string
     */
    public function getTable()
    {
        return 'usuario';
    }

    /**
     * Find a user by email for authentication.
     *
     * @param  string  $email
     * @return \App\Models\User|null
     */
    public function findForAuthentication($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'email';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'pregunta_secreta',
        'respuesta_secreta',
        'telefono',
        'facebook_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get pregunta_secreta as array (decode JSON string).
     * MongoDB almacena pregunta_secreta como string JSON.
     *
     * @param  mixed  $value
     * @return array|null
     */
    public function getPreguntaSecretaAttribute($value)
    {
        if ($value === null) {
            return null;
        }
        
        // Si ya es un array, devolverlo
        if (is_array($value)) {
            return $value;
        }
        
        // Si es string, decodificarlo como JSON
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return $decoded !== null ? $decoded : $value;
        }
        
        return $value;
    }

    /**
     * Set pregunta_secreta as JSON string (encode array).
     * MongoDB almacena pregunta_secreta como string JSON.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setPreguntaSecretaAttribute($value)
    {
        // Si es un array, codificarlo como JSON
        if (is_array($value)) {
            $this->attributes['pregunta_secreta'] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            // Si ya es string, guardarlo tal cual
            $this->attributes['pregunta_secreta'] = $value;
        }
    }

    /**
     * Get pregunta_secreta as array (helper method).
     * Siempre devuelve un array, decodificando el JSON si es necesario.
     * Compatible con MongoDB Laravel y maneja caracteres Unicode escapados.
     *
     * @return array|null
     */
    public function getPreguntaSecretaArray(): ?array
    {
        // Obtener el valor directamente desde los atributos del modelo
        // En MongoDB Laravel, los atributos están en $this->attributes
        $value = $this->attributes['pregunta_secreta'] ?? null;
        
        if ($value === null) {
            return null;
        }
        
        // Si ya es un array, devolverlo directamente
        if (is_array($value)) {
            return $value;
        }
        
        // Si es string, intentar decodificarlo como JSON
        if (is_string($value)) {
            // json_decode maneja automáticamente los caracteres Unicode escapados (\u00bf para ¿, etc.)
            // Ejemplo: "{\"pregunta\":\"\\u00bfCu\\u00e1l es el nombre de tu primera mascota?\",\"respuesta\":\"Doki\"}"
            $decoded = json_decode($value, true);
            
            // Si la decodificación fue exitosa y es un array, devolverlo
            if (is_array($decoded)) {
                return $decoded;
            }
            
            // Si falló y el valor original es un string JSON válido pero con formato diferente,
            // json_decode() debería manejarlo. Si no, devolvemos null.
            // json_decode maneja correctamente los caracteres Unicode escapados en el JSON.
        }
        
        return null;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * MongoDB usa ObjectId, necesitamos convertirlo a string.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return (string) $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
