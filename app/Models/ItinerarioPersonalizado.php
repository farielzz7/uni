<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItinerarioPersonalizado extends Model
{
    use HasFactory;

    protected $table = 'itinerarios_personalizados';

    protected $fillable = [
        'id_usuario', 'id_paquete', 'nombre', 'descripcion', 'presupuesto_total', 'detalles_json',
    ];

    protected $casts = [
        'detalles_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }
}
