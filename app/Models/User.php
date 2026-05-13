<?php

namespace App\Models;

//modelo principal de usuarios con autenticacion Laravel, roles Spatie y contrasena hasheada automaticamente
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    //campos que se pueden asignar de manera masiva
    protected $fillable = ['usuario', 'password', 'rol'];

    //campos ocultos en serializacion del modelo
    protected $hidden = ['password', 'remember_token'];

    //el campo password (se hashea automaticamente al asignarse)
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
