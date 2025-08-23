<?php

namespace App\Http\Controllers;

use App\Contracts\TMDBServiceInterface;
use App\Exceptions\TMDBException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\HandlesControllerExceptions;

class MoviesController extends Controller
{
    use HandlesControllerExceptions;

    protected TMDBServiceInterface $tmdbService;

    public function __construct(TMDBServiceInterface $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'query' => 'required|string|min:1|max:255',
                'page' => 'integer|min:1|max:1000',
                'include_adult' => 'boolean',
            ]);

            $query = $request->get('query');
            $page = $request->get('page', 1);
            $includeAdult = $request->get('include_adult', false);

            $result = $this->tmdbService->searchMovies($query, $page, $includeAdult);

            return response()->json([
                'data' => $result->toArray(),
            ]);
        } catch (TMDBException $e) {
            return $this->handleTMDBException($e, 'search');
        } catch (\Exception $e) {
            return $this->handleGenericException($e, 'movie search');
        }
    }

    public function genres(): JsonResponse
    {
        try {
            $genres = $this->tmdbService->getMovieGenres();

            return response()->json([
                'data' => array_map(fn($genre) => $genre->toArray(), $genres),
            ]);
        } catch (TMDBException $e) {
            return $this->handleTMDBException($e, 'genres');
        } catch (\Exception $e) {
            return $this->handleGenericException($e, 'genres');
        }
    }

    public function trending(): JsonResponse
    {
        try {
            $movies = $this->tmdbService->getTrendingMovies();

            return response()->json([
                'data' => array_map(fn($movie) => $movie->toArray(), $movies),
            ]);
        } catch (TMDBException $e) {
            return $this->handleTMDBException($e, 'trending');
        } catch (\Exception $e) {
            return $this->handleGenericException($e, 'trending');
        }
    }

    public function show(int $movieId): JsonResponse
    {
        try {
            $movie = $this->tmdbService->getMovie($movieId);

            return response()->json([
                'data' => $movie->toArray(),
            ]);
        } catch (TMDBException $e) {
            return $this->handleTMDBException($e, 'movie show', $movieId);
        } catch (\Exception $e) {
            return $this->handleGenericException($e, 'movie show', $movieId);
        }
    }
}
