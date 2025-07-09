<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    protected $table = 'users';
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function turista()
    {
        return $this->hasOne(Turista::class, 'id_usuario');
    }

    public function sesiones()
    {
        return $this->hasMany(Sesion::class, 'id_usuario');
    }

    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class, 'id_usuario');
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuarioxrol', 'id_usuario', 'id_rol');
    }
}
