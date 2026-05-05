<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BibliotecasSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('bibliotecas')->insert([
            ['usuario_id' => 2],
            ['usuario_id' => 3],
        ]);

        DB::table('biblioteca_libro')->insert([
            [
                'biblioteca_id' => 1,
                'libro_id' => 1,
                'estado_lectura' => 'leido'
            ],
            [
                'biblioteca_id' => 1,
                'libro_id' => 2,
                'estado_lectura' => 'leyendo'
            ],
            [
                'biblioteca_id' => 2,
                'libro_id' => 3,
                'estado_lectura' => 'pendiente'
            ],
        ]);
    }
}
