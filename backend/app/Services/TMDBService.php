<?php

namespace App\Services;

use App\Contracts\HttpClientInterface;
use App\Contracts\TMDBServiceInterface;
use App\DTOs\GenreDTO;
use App\DTOs\MovieDTO;
use App\DTOs\MovieSearchResponseDTO;
use App\Exceptions\TMDBException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TMDBService implements TMDBServiceInterface
{
    protected HttpClientInterface $httpClient;
    protected string $language;
    protected int $cacheTtl;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->language = config('services.tmdb.language', 'pt-BR');
        $this->cacheTtl = config('services.tmdb.cache_ttl', 3600);
    }

    public function searchMovies(string $query, int $page = 1, bool $includeAdult = false): MovieSearchResponseDTO
    {
        $cacheKey = "tmdb_search_{$query}_{$page}_{$includeAdult}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($query, $page, $includeAdult) {
            try {
                $response = $this->httpClient->get('/search/movie', [
                    'query' => $query,
                    'page' => $page,
                    'include_adult' => $includeAdult ? 'true' : 'false',
                    'language' => $this->language,
                ]);

                return MovieSearchResponseDTO::fromArray($response);
            } catch (TMDBException $e) {
                Log::error('Failed to search movies', [
                    'query' => $query,
                    'page' => $page,
                    'include_adult' => $includeAdult,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }

    public function getMovieGenres(): array
    {
        $cacheKey = 'tmdb_genres_movie';

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            try {
                $response  = $this->httpClient->get('/genre/movie/list', [
                    'language' => $this->language,
                ]);

                $genres = [];
                if (isset($response['genres']) && is_array($response['genres'])) {
                    foreach ($response['genres'] as $genre) {
                        $genres[] = GenreDTO::fromArray($genre);
                    }
                }

                return $genres;
            } catch (TMDBException $e) {
                Log::error('Failed to get movie genres', [
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }

    public function getTrendingMovies(): array
    {
        $cacheKey = 'tmdb_trending_movie_day';

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            try {
                $response = $this->httpClient->get('/trending/movie/day', [
                    'language' => $this->language,
                ]);

                $movies = [];
                if (isset($response['results']) && is_array($response['results'])) {
                    foreach ($response['results'] as $movie) {
                        $movies[] = MovieDTO::fromArray($movie);
                    }
                }

                return $movies;
            } catch (TMDBException $e) {
                Log::error('Failed to get trending movies', [
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }

    public function getMovie(int $movieId): MovieDTO
    {
        $cacheKey = "tmdb_movie_{$movieId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($movieId) {
            try {
                $response = $this->httpClient->get("/movie/{$movieId}", [
                    'language' => $this->language,
                ]);

                return MovieDTO::fromArray($response);
            } catch (TMDBException $e) {
                Log::error('Failed to get movie', [
                    'movie_id' => $movieId,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }

    public function getMovieByGenre(int $genreId): array
    {
        $cacheKey = "tmdb_movie_genre_{$genreId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($genreId) {
            try {
                $response = $this->httpClient->get("discover/movie?with_genres=${genreId}", [
                    'language' => $this->language,
                ]);

                $movies = [];
                if (isset($response['results']) && is_array($response['results'])) {
                    foreach ($response['results'] as $movie) {
                        $movies[] = MovieDTO::fromArray($movie);
                    }
                }

                return $movies;
            } catch (TMDBException $e) {
                Log::error('Failed to get movie', [
                    'movie_genre_id' => $genreId,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }
}
