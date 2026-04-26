<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model {
    protected $table = 'resenas';

    protected $fillable = [
        'usuario_id',
        'isbn',
        'puntuacion',
        'comentario',
        'fecha',
    ];

    public $timestamps = false;

    // Relacion con usuario
    public function usuario() {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Relacion con libro
    public function libro() {
        return $this->belongsTo(Libro::class, 'isbn', 'isbn');
    }
}
