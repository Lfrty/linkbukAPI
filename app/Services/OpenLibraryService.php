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
        // La busco en edición primero
        $desc = $edition['description'] ?? $work['description'] ?? null;

        if (!$desc) {
            return null;
        }

        // Reviso si está en value
        if (is_array($desc)) {
            $desc = $desc['value'] ?? null;
        }

        return $this->cleanDescription($desc);
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

    private function cleanDescription(?string $text): ?string {
        if (!$text) {
            return null;
        }

        // 1. CORTAR EL RUIDO: Si aparece el separador de Open Library o frases de relleno,
        // tiramos todo lo que viene después.
        $separadores = [
            '/----------/i',
            '/Also contained in:/i',
            '/Contains:/i',
            '/Source:/i'
        ];

        foreach ($separadores as $patron) {
            $partes = preg_split($patron, $text);
            $text = $partes[0]; // Nos quedamos solo con la primera parte
        }

        // 2. LIMPIAR ENLACES MARKDOWN:
        // Convierte [Texto del enlace](url) en solo "Texto del enlace"
        $text = preg_replace('/\[(.*?)\]\(.*?\)/', '$1', $text);

        // 3. LIMPIAR CORCHETES SOLITARIOS:
        // Quita cosas como [1], [2] o [edit] que sobran
        $text = preg_replace('/\[.*?\]/', '', $text);

        // 4. NORMALIZAR ESPACIOS Y SALTOS:
        // Cambiamos todos los \r, \n y \t por un espacio simple
        $text = str_replace(["\r", "\n", "\t"], ' ', $text);

        // 5. COLAPSAR ESPACIOS DOBLES:
        // Convierte "muchos     espacios" en "un espacio"
        $text = preg_replace('/\s+/', ' ', $text);

        // 6. LIMPIEZA FINAL
        $text = trim($text);

        // Opcional: Si después de limpiar queda un texto ridículamente corto (ej. " : "),
        // mejor devolvemos null para no ensuciar la interfaz.
        return (strlen($text) > 5) ? $text : null;
    }

}
