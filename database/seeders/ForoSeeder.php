<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForoSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('temas_foro')->insert([
            [
                'usuario_id' => 2,
                'titulo' => 'Mejor libro de ciencia ficción',
                'tipo' => 'debate',
                'contenido' => '¿Cuál recomiendan?'
            ],
        ]);

        DB::table('respuestas_foro')->insert([
            [
                'tema_id' => 1,
                'usuario_id' => 3,
                'contenido' => 'Dune sin duda'
            ],
        ]);

        DB::table('likes_tema')->insert([
            ['usuario_id' => 3, 'tema_id' => 1],
        ]);
    }
}
