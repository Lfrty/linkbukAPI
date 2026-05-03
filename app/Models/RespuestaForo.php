<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RespuestaForo extends Model {
    use HasFactory;

    protected $table = 'respuestas_foro';

    protected $fillable = [
        'tema_id',
        'usuario_id',
        'contenido',
    ];

    public function tema() {
        return $this->belongsTo(TemaForo::class, 'tema_id');
    }

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function tieneAcceso(Usuario $usuario): bool {
        // Es el autor o es un administrador
        return $this->usuario_id === $usuario->id || $usuario->esAdmin();
    }
}
