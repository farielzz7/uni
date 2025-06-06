<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesion extends Model
{
    protected $table = 'sesiones';

    protected $fillable = [
        'id_usuario', 'token', 'ip', 'navegador', 'fecha_inicio', 'fecha_expiracion',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}