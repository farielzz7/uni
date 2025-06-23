<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoServicio extends Model
{
    protected $table = 'tipos_servicio';

    protected $fillable = [
        'id_servicio', 'nombre', 'descripcion',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }
}