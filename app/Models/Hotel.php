<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'nombre', 'direccion', 'telefono', 'email', 'id_destino',
    ];

    public function destino()
    {
        return $this->belongsTo(Destino::class, 'id_destino');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_hotel');
    }
}