<?php

namespace App\Contracts;

use App\DTOs\MovieDTO;
use App\DTOs\MovieSearchResponseDTO;

interface TMDBServiceInterface
{

    public function searchMovies(string $query, int $page = 1, bool $includeAdult = false): MovieSearchResponseDTO;

    public function getMovieGenres(): array;

    public function getTrendingMovies(): array;

    public function getMovie(int $movieId): MovieDTO;

    public function getMovieByGenre(int $genreId): array;
}
