<?php

namespace App\Decorators;

use App\Contracts\MovieProviderInterface;
use App\DTO\PaginatedMoviesDTO;
use App\DTO\GenreDTO;
use Illuminate\Support\Facades\Cache;

class CachedMovieProvider implements MovieProviderInterface
{
    public function __construct(
        private readonly MovieProviderInterface $inner,
        private readonly int $ttlSeconds
    ) {
    }

    public function search(string $query, int $page = 1, ?string $language = null): PaginatedMoviesDTO
    {
        $cacheKey = sprintf('movies.search.%s.%d.%s', md5($query), $page, $language ?? 'default');
        return Cache::remember($cacheKey, $this->ttlSeconds, fn () => $this->inner->search($query, $page, $language));
    }

    public function genres(?string $language = null): array
    {
        $cacheKey = sprintf('movies.genres.%s', $language ?? 'default');
        return Cache::remember($cacheKey, $this->ttlSeconds, fn () => $this->inner->genres($language));
    }

    public function trending(string $window = 'day', int $page = 1, ?string $language = null): PaginatedMoviesDTO
    {
        $cacheKey = sprintf('movies.trending.%s.%d.%s', $window, $page, $language ?? 'default');
        return Cache::remember($cacheKey, $this->ttlSeconds, fn () => $this->inner->trending($window, $page, $language));
    }
}