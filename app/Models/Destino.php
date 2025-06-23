<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destino extends Model
{
    protected $fillable = [
        'nombre', 'descripcion', 'eventos', 'atractivos',
    ];

    public function categorias()
    {
        return $this->belongsToMany(CategoriaDestino::class, 'destino_categoria', 'id_destino', 'id_categoria');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'id_destino');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenDestino::class, 'id_destino');
    }

    public function hoteles()
    {
        return $this->hasMany(Hotel::class, 'id_destino');
    }
}
