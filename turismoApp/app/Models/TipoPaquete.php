<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPaquete extends Model
{
    protected $table = 'tipos_paquete';

    protected $fillable = [
        'nombre', 'descripcion',
    ];

    public function paquetes()
    {
        return $this->hasMany(Paquete::class, 'id_tipo_paquete');
    }
}
