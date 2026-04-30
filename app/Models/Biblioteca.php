<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biblioteca extends Model {
    protected $table = 'bibliotecas';

    protected $fillable = [
        'usuario_id',
    ];

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function biblioteca() {
        return $this->hasOne(Biblioteca::class, 'usuario_id');
    }

    public function libros() {
        return $this->belongsToMany(
            Libro::class,
            'biblioteca_libro',
            'biblioteca_id',
            'libro_id'
        )->withPivot(
            'estado_lectura',
            'fecha_finalizacion'
        )->withTimestamps();
    }
}
