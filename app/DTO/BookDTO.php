<?php

namespace App\DTO;

class BookDTO {
    public function __construct(
        public ?string $titulo,
        public ?string $descripcion,
        public ?array $autores,
        public ?int $paginas,
        public ?string $anyo_publicacion,
        public ?int $portada,
        public ?string $idioma,
        public array $subjects = []
    ) {
    }

    public function toArray(): array {
        return [
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'autores' => $this->autores,
            'paginas' => $this->paginas,
            'anyo_publicacion' => $this->anyo_publicacion,
            'portada' => $this->portada,
            'idioma' => $this->idioma,
            'subjects' => $this->subjects,
        ];
    }
}
