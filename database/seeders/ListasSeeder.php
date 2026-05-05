<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ListasSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('listas')->insert([
            [
                'usuario_id' => 2,
                'nombre' => 'Favoritos'
            ],
            [
                'usuario_id' => 3,
                'nombre' => 'Pendientes'
            ],
        ]);

        DB::table('lista_libro')->insert([
            [
                'lista_id' => 1,
                'libro_id' => 1
            ],
            [
                'lista_id' => 1,
                'libro_id' => 2
            ],
        ]);
    }
}
