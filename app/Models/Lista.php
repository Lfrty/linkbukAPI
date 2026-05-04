<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lista extends Model {
    use HasFactory;

    protected $table = 'listas';

    protected $fillable = [
        'usuario_id',
        'nombre',
        'es_default',
    ];

    protected $casts = [
        'es_default' => 'boolean',
    ];

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function libros() {
        return $this->belongsToMany(
            Libro::class,
            'lista_libro',
            'lista_id',
            'libro_id'
        );
    }

    public function listas() {
        return $this->belongsToMany(Lista::class, 'lista_libro', 'libro_id', 'lista_id');
    }
}
