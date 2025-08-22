<?php

namespace App\DTO;

class PaginatedMoviesDTO
{
    /** @param MovieDTO[] $results */
    public function __construct(
        public readonly int $page,
        public readonly int $totalPages,
        public readonly int $totalResults,
        public readonly array $results,
    ) {
    }

    public static function fromTmdb(array $payload): self
    {
        $results = array_map(
            fn (array $item) => MovieDTO::fromTmdb($item),
            $payload['results'] ?? []
        );

        return new self(
            page: (int) ($payload['page'] ?? 1),
            totalPages: (int) ($payload['total_pages'] ?? 1),
            totalResults: (int) ($payload['total_results'] ?? count($results)),
            results: $results,
        );
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'total_pages' => $this->totalPages,
            'total_results' => $this->totalResults,
            'results' => array_map(fn (MovieDTO $m) => $m->toArray(), $this->results),
        ];
    }
}