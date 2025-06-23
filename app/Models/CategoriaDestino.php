<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaDestino extends Model
{
    protected $table = 'categorias_destino';

    protected $fillable = [
        'nombre', 'descripcion',
    ];

    public function destinos()
    {
        return $this->belongsToMany(Destino::class, 'destino_categoria', 'id_categoria', 'id_destino');
    }
}