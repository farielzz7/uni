<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turista extends Model
{
    protected $fillable = [
        'nombre', 'apellido', 'nacionalidad', 'edad', 'telefono', 'id_usuario',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'id_turista');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_turista');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_turista');
    }
}