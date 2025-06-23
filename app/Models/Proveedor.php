<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $fillable = [
        'nombre', 'descripcion', 'contacto',
    ];

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'servicio_proveedor', 'id_proveedor', 'id_servicio');
    }
}
