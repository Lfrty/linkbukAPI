<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Libro extends Model {
    protected $table = 'libros';

    protected $fillable = [
        'work_key',
        'titulo',
        'autor',
        'anyo_publicacion',
        'descripcion',
        'portada',
        'paginas',
    ];

    // Relación: Un libro puede estar en muchas bibliotecas de usuarios
    public function usuarios() {
        return $this->belongsToMany(Usuario::class, 'bibliotecas')
                    ->withPivot('estado', 'puntuacion', 'resena')
                    ->withTimestamps();
    }

    public function bibliotecas() {
        return $this->belongsToMany(Biblioteca::class, 'biblioteca_libro')
                    ->withPivot('estado_lectura', 'fecha_finalizacion')
                    ->withTimestamps();
    }
}
