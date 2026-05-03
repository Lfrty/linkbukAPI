<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

class OpenLibraryService {
    private string $baseUrl;

    private const ENDPOINT_WORKS = '/works/';
    private const ENDPOINT_SEARCH = '/search.json';

    public function __construct() {
        $this->baseUrl = config('services.book_archive.url');
    }

    // Buscar por título
    public function search(string $query, int $limit = 10) {
        $response = Http::timeout(5) // Máximo 5 segundos esperando
        ->retry(2, 100)        // Si falla, reintenta 2 veces con 100ms de margen
        ->get($this->baseUrl . self::ENDPOINT_SEARCH, [
            'q' => $query,
            'limit' => $limit,
        ]);

        if (!$response->successful()) {
            \Log::error("Error en OpenLibraryService: " . $response->status());
            return null;
        }

        return $response->json();
    }

    // Buscar por id de work
    public function getWork(string $workKey) {
        // Por si manda /works

        $workKey = $this->cleanKey($workKey);

        $response = Http::get($this->baseUrl . self::ENDPOINT_WORKS . $workKey . '.json');

        if (!$response->successful()) {
            \Log::error("Error en OpenLibraryService: " . $response->status());
            return null;
        }

        return $response->json();
    }


    public function getEditions(string $workKey) {

        $workKey = $this->cleanKey($workKey);
        $response = Http::get($this->baseUrl . self::ENDPOINT_WORKS . $workKey . '/editions.json');

        if (!$response->successful()) {
            \Log::error("Error en OpenLibraryService: " . $response->status());
            return null;
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
        $authors = [];
        $keysToFetch = [];

        // Primero busco en caché
        foreach ($keys as $key) {
            if ($cached = cache()->get("author_{$key}")) {
                $authors[] = $cached['name'];
            } else {
                $keysToFetch[] = $key;
            }
        }

        // Pido pool de lo que NO está en caché
        if (!empty($keysToFetch)) {
            $responses = Http::pool(
                fn (Pool $pool) =>
                collect($keysToFetch)->map(fn ($key) => $pool->as($key)->get($this->baseUrl . $key . '.json'))
            );

            foreach ($responses as $key => $res) {
                if ($res->successful()) {
                    $data = $res->json();
                    cache()->put("author_{$key}", $data, 86400);
                    $authors[] = $data['name'] ?? null;
                }
            }
        }

        return array_filter($authors);
    }

    public function resolvePublishDate(array $editions): ?string {
        return collect($editions)
            ->pluck('publish_date')
            ->filter()
            ->sort()
            ->first();
    }

    private function getAuthor(string $authorKey): ?array {
        // Cacheo los autores por posibles búsquedas repetidas
        return cache()->remember("author_{$authorKey}", 86400, function () use ($authorKey) {
            return Http::get($this->baseUrl . $authorKey . '.json')->json();
        });
    }

    private function extractAuthorKeys(array $work): array {
        return collect($work['authors'] ?? [])
            ->pluck('author.key')
            ->filter()
            ->toArray();
    }

    private function cleanKey(string $key): string {
        return str_replace('/works/', '', $key);
    }

}
