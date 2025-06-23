<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Comentario extends Model
{
    protected $fillable = [
        'id_turista', 'id_destino', 'texto', 'calificacion', 'fecha',
    ];

    public function turista()
    {
        return $this->belongsTo(Turista::class, 'id_turista');
    }

    public function destino()
    {
        return $this->belongsTo(Destino::class, 'id_destino');
    }
}