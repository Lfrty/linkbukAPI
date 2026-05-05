<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuariosSeeder extends Seeder {
    public function run(): void {
        DB::table('usuarios')->insert([
            [
                'email' => 'admin@test.com',
                'password' => bcrypt('1234'),
                'nombre' => 'Admin',
                'biografia' => 'Administrador del sistema',
                'ubicacion' => 'Madrid',
                'foto_perfil' => 'admin.jpg',
                'permitir_desconocidos' => 1,
                'rol_id' => 1
            ],
            [
                'email' => 'supervisor@test.com',
                'password' => bcrypt('1234'),
                'nombre' => 'Supervisor',
                'biografia' => 'Control de contenido',
                'ubicacion' => 'Barcelona',
                'foto_perfil' => 'super.jpg',
                'permitir_desconocidos' => 1,
                'rol_id' => 2
            ],
            [
                'email' => 'user1@test.com',
                'password' => bcrypt('1234'),
                'nombre' => 'Usuario Uno',
                'biografia' => 'Amante de la lectura',
                'ubicacion' => 'Valencia',
                'foto_perfil' => 'u1.jpg',
                'permitir_desconocidos' => 1,
                'rol_id' => 3
            ],
            [
                'email' => 'user2@test.com',
                'password' => bcrypt('1234'),
                'nombre' => 'Usuario Dos',
                'biografia' => 'Fan de ciencia ficción',
                'ubicacion' => 'Sevilla',
                'foto_perfil' => 'u2.jpg',
                'permitir_desconocidos' => 0,
                'rol_id' => 3
            ],
            [
                'email' => 'user3@test.com',
                'password' => bcrypt('1234'),
                'nombre' => 'Usuario Tres',
                'biografia' => 'Lector casual',
                'ubicacion' => 'Bilbao',
                'foto_perfil' => 'u3.jpg',
                'permitir_desconocidos' => 1,
                'rol_id' => 3
            ],
        ]);
    }
}
