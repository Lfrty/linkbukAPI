<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            RolesSeeder::class,
            UsuariosSeeder::class,
            LibrosSeeder::class,
            BibliotecasSeeder::class,
            ListasSeeder::class,
            ForoSeeder::class,
        ]);
    }
}
