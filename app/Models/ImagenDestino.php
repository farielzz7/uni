<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenDestino extends Model
{
    protected $table = 'imagenes_destino';

    protected $fillable = [
        'id_destino', 'url_imagen', 'es_principal', 'descripcion',
    ];

    public function destino()
    {
        return $this->belongsTo(Destino::class, 'id_destino');
    }
}