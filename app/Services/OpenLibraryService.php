<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenLibraryService {
    private string $baseUrl;

    private const ENDPOINT_WORKS = '/works/';
    private const ENDPOINT_SEARCH = '/search.json';

    public function __construct() {
        $this->baseUrl = config('services.book_archive.url');
    }

    // Buscar por título
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

    // Buscar por id de work
    public function getWork(string $workKey) {
        // Por si manda /works

        $workKey = str_replace('/works/', '', $workKey);

        $response = Http::get($this->baseUrl . self::ENDPOINT_WORKS . $workKey . '.json');

        if (!$response->successful()) {
            return [];
        }

        return $response->json();
    }


    public function getEditions(string $workKey) {
        $response = Http::get($this->baseUrl . self::ENDPOINT_WORKS . $workKey . '/editions.json');

        if (!$response->successful()) {
            return [];
        }

        return $response->json();
    }

    public function getDescription(array $work, ?array $edition): ?string {
        $desc = $work['description'] ?? null;

        if (is_array($desc)) {
            $desc = $desc['value'] ?? null;
        }

        if (!empty($desc)) {
            return $desc;
        }

        if (!$edition) {
            return null;
        }

        $edDesc = $edition['description'] ?? null;

        if (is_array($edDesc)) {
            $edDesc = $edDesc['value'] ?? null;
        }

        return $edDesc;
    }

    public function resolveAuthors(array $work): array {
        $keys = $this->extractAuthorKeys($work);

        return collect($keys)
            ->map(function ($key) {
                $author = $this->getAuthor($key);
                return $author['name'] ?? null;
            })
            ->filter()
            ->values()
            ->toArray();
    }

    public function resolvePublishDate(array $editions): ?string {
        return collect($editions)
            ->pluck('publish_date')
            ->filter()
            ->sort()
            ->first();
    }

    private function getAuthor(string $authorKey): ?array {
        return Http::get($this->baseUrl . $authorKey . '.json')->json();
    }

    private function extractAuthorKeys(array $work): array {
        return collect($work['authors'] ?? [])
            ->pluck('author.key')
            ->filter()
            ->toArray();
    }

}
