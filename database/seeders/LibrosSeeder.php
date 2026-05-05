<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Libro; // Asegúrate de que tu modelo se llame Libro

class LibrosSeeder extends Seeder {
    public function run(): void {
        $libros = [
            [
                'titulo' => 'El Señor de los Anillos',
                'work_key' => 'OL27448W',
                'autor' => 'J.R.R. Tolkien',
                'anyo_publicacion' => 1954,
                'descripcion' => 'Una épica aventura en la Tierra Media.',
                'portada' => '12642533',
                'paginas' => 1216
            ],
            [
                'titulo' => 'Cien años de soledad',
                'work_key' => 'OL45583W',
                'autor' => 'Gabriel García Márquez',
                'anyo_publicacion' => 1967,
                'descripcion' => 'La historia de la familia Buendía en Macondo.',
                'portada' => '12818862-L',
                'paginas' => 471
            ],
            [
                'titulo' => '1984',
                'work_key' => 'OL1168083W',
                'autor' => 'George Orwell',
                'anyo_publicacion' => 1949,
                'descripcion' => 'Una distopía sobre el control totalitario y el Gran Hermano.',
                'portada' => '12711014',
                'paginas' => 328
            ],
            [
                'titulo' => 'El Principito',
                'work_key' => 'OL15181216W',
                'autor' => 'Antoine de Saint-Exupéry',
                'anyo_publicacion' => 1943,
                'descripcion' => 'Un niño viaja por planetas aprendiendo sobre la vida.',
                'portada' => '12534575',
                'paginas' => 96
            ]
        ];

        foreach ($libros as $libro) {
            Libro::updateOrCreate(['work_key' => $libro['work_key']], $libro);
        }
    }
}
