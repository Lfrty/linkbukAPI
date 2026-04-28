<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenLibraryService;
use App\Services\EditionResolver;
use App\DTO\BookDTO;

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
                    'title' => $book['title'] ?? null,
                    'work_key' => $book['key'] ?? null,
                    'authors' => $book['author_name'] ?? [],
                    'cover' => $book['cover_i'] ?? null,
                    'year' => $book['first_publish_year'] ?? null,
                ];
            })
            ->take(10)
            ->values();
    }

    public function show(
        string $workKey,
        OpenLibraryService $service,
        EditionResolver $resolver
    ) {
        $work = $service->getWork($workKey);
        $editions = $service->getEditions($workKey);

        $bestEdition = $resolver->resolveBest($editions['entries'] ?? []);

        $dto = new BookDTO(
            title: $resolver->resolveTitle($work, $bestEdition),
            description: $service->getDescription($work, $bestEdition),
            authors: $service->resolveAuthors($work),
            pages: $bestEdition['number_of_pages'] ?? null,
            publishDate: $service->resolvePublishDate($editions['entries'] ?? []),
            cover: $resolver->resolveCover($work, $bestEdition),
            language: $resolver->resolveLanguage($bestEdition),
            subjects: $work['subjects'] ?? []
        );

        return response()->json($dto->toArray());
    }

}
