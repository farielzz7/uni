<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoPaquete extends Model
{
    protected $table = 'pago_paquete';

    protected $fillable = [
        'id_paquete', 'monto', 'fecha_pago', 'estado_pago', 'id_metodo_pago', 'referencia_pago',
    ];

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_pago');
    }
}