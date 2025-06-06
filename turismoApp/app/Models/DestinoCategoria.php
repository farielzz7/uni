<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestinoCategoria extends Model
{
    protected $table = 'destino_categoria';

    protected $fillable = [
        'id_destino', 'id_categoria',
    ];

    public function destino()
    {
        return $this->belongsTo(Destino::class, 'id_destino');
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaDestino::class, 'id_categoria');
    }
}
