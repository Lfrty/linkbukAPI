<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenLibraryService;
use App\Services\EditionResolver;
use App\DTO\BookDTO;
use App\Models\Libro;

class LibroController extends Controller {
    private const NUM_RESULTADOS = 5;
    // Búsqueda en API
    public function search(Request $request, OpenLibraryService $service) {
        $query = $request->input('q');

        $data = $service->search($query);

        return $this->successResponse($this->formatResults($data));
    }

    // Controla y devuelve los datos de un libro
    public function show(
        string $workKey,
        Request $request,
        OpenLibraryService $service,
        EditionResolver $resolver
    ) {
        //Busco en base de datos si existe mediante work_key
        $libro = $this->buscarLibro_BD($workKey);

        if ($libro) {
            return $this->successResponse($libro, 'Libro recuperado de la BD');
        }
        $libro = $this->buscarLibro_BD($workKey);

        // Si no está en BD capturo lo que ya se saca en la propia búsqeda
        $datosPrevios = [
            'titulo'  => $request->query('titulo_search'),
            'autores' => $request->query('autores_search'), // Espera un array o string
            'portada' => $request->query('portada_search'),
            'anyo'    => $request->query('anyo_search')
        ];

        // Busco en la api por work_key
        $dto = $this->getDataLibro_Api($workKey, $service, $resolver, $datosPrevios);

        // Lo guardo en BD
        $libro = $this->guardarLibro_BD($workKey, $dto);

        return $this->successResponse($libro, 'Libro importado de API y guardado');
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
        EditionResolver $resolver,
        array $datosPrevios = []
    ): BookDTO {
        $work = $service->getWork($workKey);
        $editions = $service->getEditions($workKey);

        $bestEdition = $resolver->resolveBest($editions['entries'] ?? []);

        // De no tener el dato previo lo tarto de sacar de la mejor edición encontrada
        $tituloFinal = $datosPrevios['titulo'] ?? $resolver->resolveTitle($work, $bestEdition);

        // Autores: si vienen de la búsqueda, los usamos. Si no, resolvemos IDs.
        $autoresFinal = !empty($datosPrevios['autores'])
            ? (is_array($datosPrevios['autores']) ? $datosPrevios['autores'] : [$datosPrevios['autores']])
            : $service->resolveAuthors($work);

        $portadaFinal = $datosPrevios['portada'] ?? $resolver->resolveCover($work, $bestEdition);

        $anyoCrudo = $datosPrevios['anyo'] ?? $service->resolvePublishDate($editions['entries'] ?? []);
        $anyoFinal = $anyoCrudo ? (int) $this->getAnyo((string)$anyoCrudo) : null;

        return new BookDTO(
            titulo:           $tituloFinal,
            descripcion:      $service->getDescription($work, $bestEdition),
            autores:          $autoresFinal,
            paginas:          $resolver->resolvePages($editions['entries'] ?? [], $bestEdition),
            anyo_publicacion: $anyoFinal,
            portada:          $portadaFinal ? (string)$portadaFinal : null,
            idioma:           $resolver->resolveLanguage($bestEdition),
            subjects:         $work['subjects'] ?? []
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
            ->take(self::NUM_RESULTADOS)
            ->values();
    }

}
