<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenLibraryService {
    private string $baseUrl;

    private const ENDPOINT_WORKS = '/works';
    private const ENDPOINT_SEARCH = '/search.json';

    public function __construct() {
        $this->baseUrl = config('services.book_archive.url');
    }

    public function search(string $query, int $limit = 10) {
        $response = Http::get($this->baseUrl . self::ENDPOINT_SEARCH, [
            'q' => $query,
            'limit' => $limit,
        ]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json();
    }
}
