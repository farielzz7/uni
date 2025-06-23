<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $fillable = [
        'id_turista', 'id_hotel', 'fecha_entrada', 'fecha_salida', 'numero_personas', 'estado',
    ];

    public function turista()
    {
        return $this->belongsTo(Turista::class, 'id_turista');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel');
    }
}
