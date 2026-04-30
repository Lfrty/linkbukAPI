<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenLibraryService;
use App\Services\EditionResolver;
use App\DTO\BookDTO;
use App\Models\Libro;

class LibroController extends Controller {
    // Búsqueda en API
    public function search(Request $request, OpenLibraryService $service) {
        $query = $request->input('q');

        $data = $service->search($query);

        return response()->json($this->formatResults($data));
    }

    // Formatear JSON y devolver
    private function formatResults($data) {
        return collect($data['docs'] ?? [])
            ->map(function ($book) {
                return [
                    'titulo' => $book['title'] ?? null,
                    'work_key' => $book['key'] ?? null,
                    'autores' => $book['author_name'] ?? [],
                    'portada' => $book['cover_i'] ?? null,
                    'anyo_publicacion' => $book['first_publish_year'] ?? null,
                ];
            })
            ->take(10)
            ->values();
    }

    // Controla y devuelve los datos de un libro
    public function show(
        string $workKey,
        OpenLibraryService $service,
        EditionResolver $resolver
    ) {
        //Busco en base de datos si existe mediante work_key
        $libro = $this->buscarLibro_BD($workKey);

        if ($libro) {
            return response()->json($libro);
        }

        // Busco en la api por work_key
        $dto = $this->getDataLibro_Api($workKey, $service, $resolver);

        // Lo guardo en BD
        $libro = $this->guardarLibro_BD($workKey, $dto);

        return response()->json($libro);
    }

    // Busca libro en base de datos
    private function buscarLibro_BD(string $workKey): ?Libro {
        return Libro::where('work_key', $workKey)->first();
    }

    // Almacena libro en base d edatos
    private function guardarLibro_BD(string $workKey, BookDTO $dto): Libro {
        return Libro::create([
            'work_key' => $workKey,
            'titulo' => $dto->titulo,
            'autor' => implode(', ', $dto->autores),
            'anyo_publicacion' => $this->getAnyo($dto->anyo_publicacion),
            'descripcion' => $dto->descripcion,
            'portada' => $dto->portada,
        ]);
    }

    // Busca libro en la API
    private function getDataLibro_Api(
        string $workKey,
        OpenLibraryService $service,
        EditionResolver $resolver
    ): BookDTO {
        $work = $service->getWork($workKey);
        $editions = $service->getEditions($workKey);

        $bestEdition = $resolver->resolveBest($editions['entries'] ?? []);

        return new BookDTO(
            titulo: $resolver->resolveTitle($work, $bestEdition),
            descripcion: $service->getDescription($work, $bestEdition),
            autores: $service->resolveAuthors($work),
            paginas: $bestEdition['number_of_pages'] ?? null,
            anyo_publicacion: $service->resolvePublishDate($editions['entries'] ?? []),
            portada: $resolver->resolveCover($work, $bestEdition),
            idioma: $resolver->resolveLanguage($bestEdition),
            subjects: $work['subjects'] ?? []
        );
    }

    // Obtiene el año de la fecha extraída
    private function getAnyo(?string $date): ?int {
        if (!$date) {
            return null;
        }

        preg_match('/\b(1[0-9]{3}|20[0-9]{2}|21[0-9]{2})\b/', $date, $matches);

        return isset($matches[1]) ? (int) $matches[1] : null;
    }

}
