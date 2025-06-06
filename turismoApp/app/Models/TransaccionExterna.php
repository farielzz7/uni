<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaccionExterna extends Model
{
    protected $table = 'transacciones_externas';

    protected $fillable = [
        'id_pago', 'proveedor', 'respuesta_raw',
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'id_pago');
    }
}