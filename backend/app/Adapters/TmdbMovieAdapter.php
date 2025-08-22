<?php

namespace App\Adapters;

use App\Contracts\MovieProviderInterface;
use App\DTO\GenreDTO;
use App\DTO\PaginatedMoviesDTO;
use App\Exceptions\TmdbException;
use Illuminate\Support\Facades\Log;

class TmdbMovieAdapter implements MovieProviderInterface
{
    public function __construct(
        private readonly TmdbHttpClient $client,
        private readonly string $defaultLanguage,
    ) {
    }

    public function search(string $query, int $page = 1, ?string $language = null): PaginatedMoviesDTO
    {
        $language = $language ?: $this->defaultLanguage;

        try {
            $result = $this->client->get('/search/movie', [
                'query' => $query,
                'page' => $page,
                'include_adult' => false,
                'language' => $language,
            ]);

            if (!$result['ok']) {
                $message = is_array($result['body']) ? ($result['body']['status_message'] ?? 'Unknown error') : 'Unknown error';
                throw TmdbException::withStatus($result['status'], $message);
            }

            return PaginatedMoviesDTO::fromTmdb($result['body']);
        } catch (\Throwable $e) {
            Log::error('TMDB search failed', [
                'query' => $query,
                'page' => $page,
                'language' => $language,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function genres(?string $language = null): array
    {
        $language = $language ?: $this->defaultLanguage;

        try {
            $result = $this->client->get('/genre/movie/list', [
                'language' => $language,
            ]);

            if (!$result['ok']) {
                $message = is_array($result['body']) ? ($result['body']['status_message'] ?? 'Unknown error') : 'Unknown error';
                throw TmdbException::withStatus($result['status'], $message);
            }

            $genres = $result['body']['genres'] ?? [];
            return array_map(fn (array $g) => GenreDTO::fromTmdb($g), $genres);
        } catch (\Throwable $e) {
            Log::error('TMDB genres failed', [
                'language' => $language,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function trending(string $window = 'day', int $page = 1, ?string $language = null): PaginatedMoviesDTO
    {
        $language = $language ?: $this->defaultLanguage;

        try {
            $result = $this->client->get("/trending/movie/{$window}", [
                'language' => $language,
                'page' => $page,
            ]);

            if (!$result['ok']) {
                $message = is_array($result['body']) ? ($result['body']['status_message'] ?? 'Unknown error') : 'Unknown error';
                throw TmdbException::withStatus($result['status'], $message);
            }

            return PaginatedMoviesDTO::fromTmdb($result['body']);
        } catch (\Throwable $e) {
            Log::error('TMDB trending failed', [
                'window' => $window,
                'page' => $page,
                'language' => $language,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}