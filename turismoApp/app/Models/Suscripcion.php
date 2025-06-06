<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    protected $fillable = [
        'id_usuario', 'tipo_suscripcion', 'fecha_inicio', 'fecha_fin', 'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}