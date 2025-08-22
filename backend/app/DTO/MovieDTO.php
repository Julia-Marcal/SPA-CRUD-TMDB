<?php

namespace App\DTO;

class MovieDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $overview,
        public readonly ?string $posterPath,
        public readonly ?string $releaseDate,
        public readonly float $voteAverage,
        public readonly int $voteCount,
        public readonly array $genreIds,
        public readonly ?string $originalLanguage,
    ) {
    }

    public static function fromTmdb(array $payload): self
    {
        return new self(
            id: (int) ($payload['id'] ?? 0),
            title: (string) ($payload['title'] ?? $payload['name'] ?? ''),
            overview: $payload['overview'] ?? null,
            posterPath: $payload['poster_path'] ?? null,
            releaseDate: $payload['release_date'] ?? null,
            voteAverage: (float) ($payload['vote_average'] ?? 0.0),
            voteCount: (int) ($payload['vote_count'] ?? 0),
            genreIds: array_map('intval', $payload['genre_ids'] ?? []),
            originalLanguage: $payload['original_language'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'overview' => $this->overview,
            'poster_path' => $this->posterPath,
            'release_date' => $this->releaseDate,
            'vote_average' => $this->voteAverage,
            'vote_count' => $this->voteCount,
            'genre_ids' => $this->genreIds,
            'original_language' => $this->originalLanguage,
        ];
    }
}