<?php

namespace App\Contracts;

use App\DTO\GenreDTO;
use App\DTO\PaginatedMoviesDTO;

interface MovieProviderInterface
{
    public function search(string $query, int $page = 1, ?string $language = null): PaginatedMoviesDTO;

    /** @return GenreDTO[] */
    public function genres(?string $language = null): array;

    public function trending(string $window = 'day', int $page = 1, ?string $language = null): PaginatedMoviesDTO;
}