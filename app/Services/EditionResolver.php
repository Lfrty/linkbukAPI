<?php

namespace App\Services;

class EditionResolver {
    public function resolveBest(array $editions): ?array {
        $collection = collect($editions);

        // 1. intentar español válido
        if ($spanish = $this->findValidByLanguage($collection, 'spa')) {
            return $spanish;
        }

        // 2. inglés válido
        if ($english = $this->findValidByLanguage($collection, 'eng')) {
            return $english;
        }

        return $collection->first();
    }

    public function resolveTitle($work, $edition) {
        if (!$edition) {
            return $work['title'] ?? null;
        }

        $langs = collect($edition['languages'] ?? [])
            ->pluck('key')
            ->toArray();

        $title = $edition['title'] ?? null;

        // heurística: español + título no vacío
        if (in_array('/languages/spa', $langs) && !empty($title)) {
            return $title;
        }

        return $work['title'] ?? $title;
    }

    public function resolveLanguage($edition): ?string {
        return collect($edition['languages'] ?? [])
            ->pluck('key')
            ->map(fn ($l) => str_replace('/languages/', '', $l))
            ->first();
    }

    public function resolveCover($work, $edition): ?int {
        return $edition['covers'][0]
            ?? $work['covers'][0]
            ?? null;
    }

    // Ayuda a identificar elementos en español
    private function findValidByLanguage($collection, string $lang): ?array {
        return $collection->first(function ($ed) use ($lang) {

            $langs = collect($ed['languages'] ?? [])
                ->pluck('key')
                ->toArray();

            if (!in_array("/languages/$lang", $langs)) {
                return false;
            }

            // heurística de calidad mínima
            $hasTitle = !empty($ed['title']);
            $hasPages = !empty($ed['number_of_pages']);

            return $hasTitle || $hasPages;
        });
    }

    public function resolvePages(array $editions, ?array $bestEdition): ?int {
        if (!empty($bestEdition['number_of_pages'])) {
            return (int) $bestEdition['number_of_pages'];
        }

        // Busca la edición con más páginas (más probable completa)
        $best = collect($editions)
            ->filter(fn ($ed) => !empty($ed['number_of_pages']))
            ->sortByDesc('number_of_pages')
            ->first();

        if ($best) {
            return (int) $best['number_of_pages'];
        }

        return null;
    }
}
