<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
