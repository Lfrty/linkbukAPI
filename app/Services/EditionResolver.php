<?php

namespace App\Services;

class EditionResolver {
    // De todas las ediciones resuelve la más completa
    public function resolveBest(array $editions): ?array {
        $collection = collect($editions);

        // Intentamos buscar la mejor en español primero
        $best = $this->rankByLanguage($collection, 'spa');

        // Si no hay nada en español, intentamos en inglés
        if (!$best) {
            $best = $this->rankByLanguage($collection, 'eng');
        }

        // Si sigue sin haber nada, devolvemos la primera disponible
        return $best ?? $collection->first();
    }

    private function rankByLanguage($collection, string $lang): ?array {
        $filtered = $collection->filter(function ($ed) use ($lang) {
            $langs = collect($ed['languages'] ?? [])->pluck('key')->toArray();
            return in_array("/languages/$lang", $langs);
        });

        if ($filtered->isEmpty()) {
            return null;
        }

        return $filtered->sortByDesc(function ($ed) {
            $score = 0;
            if (!empty($ed['description'])) {
                $score += 10;
            }
            if (!empty($ed['covers'])) {
                $score += 5;
            }
            if (!empty($ed['number_of_pages'])) {
                $score += 2;
            }
            return $score;
        })->first();
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

    // Busco resultados reales y la que más se acerque
    public function resolvePages(array $editions, ?array $bestEdition): ?int {
        // Si la mejor edición ya tiene páginas reales (> 5), nos las quedamos
        if (!empty($bestEdition['number_of_pages']) && $bestEdition['number_of_pages'] > 5) {
            return (int) $bestEdition['number_of_pages'];
        }

        $best = collect($editions)
            ->map(fn ($ed) => (int) ($ed['number_of_pages'] ?? 0))
            ->filter(fn ($p) => $p > 5 && $p < 5000)
            ->max();

        return $best ?: null;
    }
}
