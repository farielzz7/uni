<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $fillable = [
        'nombre', 'descripcion', 'categoria',
    ];

    public function tiposServicio()
    {
        return $this->hasMany(TipoServicio::class, 'id_servicio');
    }

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'servicio_proveedor', 'id_servicio', 'id_proveedor');
    }

    public function paquetes()
    {
        return $this->belongsToMany(Paquete::class, 'paquete_servicio', 'id_servicio', 'id_paquete')->withPivot('cantidad', 'precio_individual');
    }
}