<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'birth_date',
        'cpf',
        'google_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Set the user's CPF.
     * 
     * @param string $value
     * @return void
     */
    public function setCpfAttribute($value)
    {
        $this->attributes['cpf'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Get the user's CPF.
     * 
     * @param string $value
     * @return string
     */
    public function getCpfAttribute($value)
    {
        if (strlen($value) === 11) {
            return substr($value, 0, 3) . '.' . 
                   substr($value, 3, 3) . '.' . 
                   substr($value, 6, 3) . '-' . 
                   substr($value, 9, 2);
        }
        return $value;
    }
}