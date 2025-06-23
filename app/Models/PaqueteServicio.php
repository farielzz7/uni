<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaqueteServicio extends Model
{
    protected $table = 'paquete_servicio';

    protected $fillable = [
        'id_paquete', 'id_servicio', 'cantidad', 'precio_individual',
    ];

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }
}