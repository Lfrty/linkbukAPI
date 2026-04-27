<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable {
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    // Nombre tablac
    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'biografia',
        'ubicacion',
        'foto_perfil',
        'permite_desconocidos',
        'rol_id',
    ];

    // Contraseña protegida al exportar
    protected $hidden = [
        'password',
    ];

    // Por defecto se crea a true
    protected $attributes = [
        'permite_desconocidos' => true,
        'biografia' => '-',
    ];

    // COnvierte a bool
    protected $casts = [
        'permite_desconocidos' => 'boolean',
    ];

    // RELACIÓN Roles
    public function rol() {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    // Relaicón listas
    public function listas() {
        return $this->hasMany(Lista::class, 'usuario_id');
    }

    protected function password(): Attribute {
        return Attribute::make(
            set: fn ($value) => bcrypt($value),
        );
    }
}
