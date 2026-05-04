<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable {
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    // Nombre tablac
    protected $table = 'usuarios';

    protected $appends = ['rol_name'];

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'biografia',
        'ubicacion',
        'foto_perfil',
        'permitir_desconocidos',
        'rol_id',
    ];

    // Contraseña protegida al exportar
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    // Por defecto se crea a true
    protected $attributes = [
        'permitir_desconocidos' => true,
        'biografia' => '-',
    ];

    public function getRolNameAttribute() {
        return match($this->rol_id) {
            1 => 'admin',
            2 => 'supervisor',
            default => 'usuario',
        };
    }

    // Convierte a bool
    protected $casts = [
        'permitir_desconocidos' => 'boolean',
        'password' => 'hashed' //Aquí la cifra
    ];

    // RELACIÓN Roles
    public function rol() {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    // Relación listas
    public function listas() {
        return $this->hasMany(Lista::class, 'usuario_id');
    }

    // Relación Reseñas
    public function resenas() {
        return $this->hasMany(Resena::class, 'usuario_id');
    }

    // Devuelve la biblioteca del usuario
    public function biblioteca(): HasOne {
        return $this->hasOne(Biblioteca::class);
    }

    //Comprobar rol Admin
    public function esAdmin() {
        return $this->rol_id === 1;
    }

    //Comprobar rol Supervisor
    public function esSupervisor() {
        return $this->rol_id === 2;
    }
}
