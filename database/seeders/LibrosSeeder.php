<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LibrosSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('libros')->insert([
            [
                'titulo' => 'Dune',
                'autor' => 'Frank Herbert',
                'isbn' => '9780441172719'
            ],
            [
                'titulo' => '1984',
                'autor' => 'George Orwell',
                'isbn' => '9780451524935'
            ],
            [
                'titulo' => 'El Hobbit',
                'autor' => 'J.R.R. Tolkien',
                'isbn' => '9780345339683'
            ],
        ]);
    }
}
