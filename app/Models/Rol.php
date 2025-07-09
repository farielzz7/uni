<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;
     protected $table = 'roles';
    protected $fillable = [
        'nombre', 'clave_rol',
    ];

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permiso', 'id_rol', 'id_permiso');
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'usuarioxrol', 'id_rol', 'id_usuario');
    }
}