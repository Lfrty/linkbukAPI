<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model {
    protected $table = 'resenas';

    protected $fillable = [
        'usuario_id',
        'libro_id',
        'puntuacion',
        'comentario',
    ];

    // Como sí tienes created_at y updated_at en la tabla:
    public $timestamps = true;

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function libro() {
        return $this->belongsTo(Libro::class, 'libro_id');
    }
}
