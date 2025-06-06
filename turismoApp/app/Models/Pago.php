<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'id_turista', 'id_paquete', 'fecha_pago', 'monto', 'estado', 'referencia_pago', 'id_metodo_pago',
    ];

    public function turista()
    {
        return $this->belongsTo(Turista::class, 'id_turista');
    }

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_pago');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class, 'id_pago');
    }

    public function transaccionExterna()
    {
        return $this->hasOne(TransaccionExterna::class, 'id_pago');
    }
}
