<?php

namespace App\DTOs;

class MovieSearchResponseDTO
{
    public function __construct(
        public readonly int $page,
        public readonly array $results,
        public readonly int $total_pages,
        public readonly int $total_results
    ) {}

    public static function fromArray(array $data): self
    {
        $results = array_map(fn($movie) => MovieDTO::fromArray($movie), $data['results'] ?? []);

        return new self(
            page: $data['page'] ?? 1,
            results: $results,
            total_pages: $data['total_pages'] ?? 0,
            total_results: $data['total_results'] ?? 0
        );
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'results' => array_map(fn($movie) => $movie->toArray(), $this->results),
            'total_pages' => $this->total_pages,
            'total_results' => $this->total_results,
        ];
    }
}
