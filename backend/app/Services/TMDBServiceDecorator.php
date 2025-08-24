<?php

namespace App\Services;

use App\Contracts\TMDBServiceInterface;
use App\DTOs\GenreDTO;
use App\DTOs\MovieDTO;
use App\DTOs\MovieSearchResponseDTO;
use Illuminate\Support\Facades\Log;

class TMDBServiceDecorator implements TMDBServiceInterface
{
    protected TMDBServiceInterface $tmdbService;

    public function __construct(TMDBServiceInterface $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function searchMovies(string $query, int $page = 1, bool $includeAdult = false): MovieSearchResponseDTO
    {
        try {
            $result = $this->tmdbService->searchMovies($query, $page, $includeAdult);

            return $result;
        } catch (\Exception $e) {
            $this->logError('search_movies', $e, [
                'query' => $query,
                'page' => $page,
                'include_adult' => $includeAdult,
            ]);
            throw $e;
        }
    }

    public function getMovieGenres(): array
    {
        try {
            $result = $this->tmdbService->getMovieGenres();

            return $result;
        } catch (\Exception $e) {
            $this->logError('get_movie_genres', $e);
            throw $e;
        }
    }

    public function getTrendingMovies(): array
    {
        try {
            $result = $this->tmdbService->getTrendingMovies();

            return $result;
        } catch (\Exception $e) {
            $this->logError('get_trending_movies', $e);
            throw $e;
        }
    }

    public function getMovie(int $movieId): MovieDTO
    {
        try {
            $result = $this->tmdbService->getMovie($movieId);

            return $result;
        } catch (\Exception $e) {
            $this->logError('get_movie', $e, [
                'movie_id' => $movieId,
            ]);
            throw $e;
        }
    }

    protected function logError(string $operation, \Exception $exception, array $context = []): void
    {
        Log::error("TMDB API {$operation} failed", array_merge($context, [
            'operation' => $operation,
            'error' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'timestamp' => now()->toISOString(),
        ]));
    }
}
