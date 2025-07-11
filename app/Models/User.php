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

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_usuario');
    }

    // Usuarios que siguen a este usuario
    public function seguidores()
    {
        return $this->belongsToMany(User::class, 'seguidores', 'user_id', 'follower_id');
    }

    // Usuarios a los que este usuario sigue
    public function siguiendo()
    {
        return $this->belongsToMany(User::class, 'seguidores', 'follower_id', 'user_id');
    }

    // Comprueba si este usuario está siguiendo a otro usuario
    public function isFollowing(User $user)
    {
        return $this->siguiendo()->where('user_id', $user->id)->exists();
    }
}
