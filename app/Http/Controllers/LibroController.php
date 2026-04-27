<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenLibraryService;

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
}
