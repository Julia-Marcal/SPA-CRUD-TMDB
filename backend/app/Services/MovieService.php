<?php

namespace App\Services;

use App\Contracts\MovieProviderInterface;

class MovieService
{
    public function __construct(private readonly MovieProviderInterface $provider)
    {
    }

    public function search(string $query, int $page = 1, ?string $language = null): array
    {
        return $this->provider->search($query, $page, $language)->toArray();
    }

    public function genres(?string $language = null): array
    {
        return array_map(fn ($g) => $g->toArray(), $this->provider->genres($language));
    }

    public function trending(string $window = 'day', int $page = 1, ?string $language = null): array
    {
        return $this->provider->trending($window, $page, $language)->toArray();
    }
}