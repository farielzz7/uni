<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soporte extends Model
{
    use HasFactory;

    protected $table = 'soportes';

    protected $fillable = [
        'id_usuario', 'asunto', 'mensaje', 'estado', 'fecha_creacion',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
