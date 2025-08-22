<?php

namespace App\Decorators;

use App\Contracts\MovieProviderInterface;
use App\DTO\PaginatedMoviesDTO;
use Illuminate\Support\Facades\Log;

class LoggedMovieProvider implements MovieProviderInterface
{
    public function __construct(private readonly MovieProviderInterface $inner)
    {
    }

    public function search(string $query, int $page = 1, ?string $language = null): PaginatedMoviesDTO
    {
        $start = microtime(true);
        try {
            $result = $this->inner->search($query, $page, $language);
            return $result;
        } finally {
            Log::info('MovieProvider.search', [
                'query' => $query,
                'page' => $page,
                'language' => $language,
                'duration_ms' => (int) ((microtime(true) - $start) * 1000),
            ]);
        }
    }

    public function genres(?string $language = null): array
    {
        $start = microtime(true);
        try {
            return $this->inner->genres($language);
        } finally {
            Log::info('MovieProvider.genres', [
                'language' => $language,
                'duration_ms' => (int) ((microtime(true) - $start) * 1000),
            ]);
        }
    }

    public function trending(string $window = 'day', int $page = 1, ?string $language = null): PaginatedMoviesDTO
    {
        $start = microtime(true);
        try {
            return $this->inner->trending($window, $page, $language);
        } finally {
            Log::info('MovieProvider.trending', [
                'window' => $window,
                'page' => $page,
                'language' => $language,
                'duration_ms' => (int) ((microtime(true) - $start) * 1000),
            ]);
        }
    }
}