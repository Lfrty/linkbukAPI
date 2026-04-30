<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TemaForo extends Model {
    use HasFactory;

    protected $table = 'temas_foro';

    protected $fillable = [
        'usuario_id',
        'titulo',
        'tipo',
        'contenido',
    ];

    // Relación con usuario
    public function usuario() {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function respuestas() {
        return $this->hasMany(RespuestaForo::class, 'tema_id');
    }
}
