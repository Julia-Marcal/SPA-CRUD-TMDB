<?php

namespace App\DTOs;

class MovieDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $overview,
        public readonly ?string $poster_path,
        public readonly ?string $backdrop_path,
        public readonly ?string $release_date,
        public readonly float $vote_average,
        public readonly int $vote_count,
        public readonly ?array $genre_ids,
        public readonly ?string $original_language,
        public readonly ?string $original_title,
        public readonly ?bool $adult,
        public readonly ?bool $video,
        public readonly ?float $popularity
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            title: $data['title'] ?? '',
            overview: $data['overview'] ?? null,
            poster_path: $data['poster_path'] ?? null,
            backdrop_path: $data['backdrop_path'] ?? null,
            release_date: $data['release_date'] ?? null,
            vote_average: $data['vote_average'] ?? 0.0,
            vote_count: $data['vote_count'] ?? 0,
            genre_ids: $data['genre_ids'] ?? null,
            original_language: $data['original_language'] ?? null,
            original_title: $data['original_title'] ?? null,
            adult: $data['adult'] ?? null,
            video: $data['video'] ?? null,
            popularity: $data['popularity'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'overview' => $this->overview,
            'poster_path' => $this->poster_path,
            'backdrop_path' => $this->backdrop_path,
            'release_date' => $this->release_date,
            'vote_average' => $this->vote_average,
            'vote_count' => $this->vote_count,
            'genre_ids' => $this->genre_ids,
            'original_language' => $this->original_language,
            'original_title' => $this->original_title,
            'adult' => $this->adult,
            'video' => $this->video,
            'popularity' => $this->popularity,
        ];
    }
}
