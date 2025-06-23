<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    protected $fillable = [
        'id_tipo_paquete', 'nombre', 'descripcion', 'precio', 'duracion_dias', 'disponible',
    ];

    public function tipoPaquete()
    {
        return $this->belongsTo(TipoPaquete::class, 'id_tipo_paquete');
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'paquete_servicio', 'id_paquete', 'id_servicio')->withPivot('cantidad', 'precio_individual');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_paquete');
    }

    public function pagosPaquete()
    {
        return $this->hasMany(PagoPaquete::class, 'id_paquete');
    }
}