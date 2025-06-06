<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Factura extends Model
{
    protected $fillable = [
        'id_pago', 'numero_factura', 'rfc_cliente', 'nombre_cliente', 'direccion_cliente', 'fecha_emision', 'subtotal', 'iva', 'total',
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'id_pago');
    }
}