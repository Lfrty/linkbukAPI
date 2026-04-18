<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Nombre tabla
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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'permite_desconocidos' => 'boolean',
        ];
    }

    // RELACIÓN Roles
    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }
}