<?php

namespace App\DTO;

class BookDTO {
    public function __construct(
        public ?string $title,
        public ?string $description,
        public ?array $authors,
        public ?int $pages,
        public ?string $publishDate,
        public ?int $cover,
        public ?string $language,
        public array $subjects = []
    ) {
    }

    public function toArray(): array {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'authors' => $this->authors,
            'pages' => $this->pages,
            'publish_date' => $this->publishDate,
            'cover' => $this->cover,
            'language' => $this->language,
            'subjects' => $this->subjects,
        ];
    }
}
