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
            $movies = $result->toArray();
            $movies['results'] = $this->addFavoriteFlag($request, $movies['results']);

            return response()->json([
                'data' => $movies,
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

    public function trending(Request $request): JsonResponse
    {
        try {
            $movies = $this->tmdbService->getTrendingMovies();
            $moviesArray = array_map(fn($movie) => $movie->toArray(), $movies);
            $moviesWithFavorite = $this->addFavoriteFlag($request, $moviesArray);

            return response()->json([
                'data' => $moviesWithFavorite,
            ]);
        } catch (TMDBException $e) {
            return $this->handleTMDBException($e, 'trending');
        } catch (\Exception $e) {
            return $this->handleGenericException($e, 'trending');
        }
    }

    public function show(Request $request, $movieId): JsonResponse
    {
        try {
            $movie = $this->tmdbService->getMovie((int) $movieId);
            $movieData = $movie->toArray();

            $user = $request->user();
            if ($user) {
                $isFavorite = $user->favoriteMovies()->where('movie_id', $movieId)->exists();
                $movieData['is_favorite'] = $isFavorite;
            } else {
                $movieData['is_favorite'] = false;
            }

            return response()->json([
                'data' => $movieData,
            ]);
        } catch (TMDBException $e) {
            return $this->handleTMDBException($e, 'movie show', $movieId);
        } catch (\Exception $e) {
            return $this->handleGenericException($e, 'movie show', $movieId);
        }
    }

    public function addFavorite(Request $request, int $movieId): JsonResponse
    {
        try {
            $user = $request->user();

            $favorite = $user->favoriteMovies()->where('movie_id', $movieId)->first();

            if ($favorite) {
                return response()->json([
                    'message' => 'Movie already in favorites',
                ], 409);
            }

            $movieData = $this->tmdbService->getMovie($movieId);

            $user->favoriteMovies()->create([
                'movie_id' => $movieId,
                'movie' => $movieData->toArray(),
            ]);

            return response()->json([
                'message' => 'Movie added to favorites',
            ], 201);
        } catch (TMDBException $e) {
            return $this->handleTMDBException($e, 'add favorite');
        } catch (\Exception $e) {
            return $this->handleGenericException($e, 'add favorite');
        }
    }

    public function removeFromFavorite(Request $request, int $movieId): JsonResponse
    {
        try {
            $user = $request->user();

            $favorite = $user->favoriteMovies()->where('movie_id', $movieId)->first();

            if (!$favorite) {
                return response()->json([
                    'message' => 'Movie not in favorites',
                ], 404);
            }

            $favorite->delete();

            return response()->json([
                'message' => 'Movie removed from favorites',
            ]);
        } catch (\Exception $e) {
            return $this->handleGenericException($e, 'remove favorite');
        }
    }

    public function getFavoriteMovies(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $favorites = $user->favoriteMovies()->get();

            $formattedMovies = $favorites->map(function ($favorite) {
                $movieData = $favorite->movie;
                if (is_string($movieData)) {
                    $movieData = json_decode($movieData, true);
                }
                return [
                    'id' => $favorite->movie_id,
                    'is_favorite' => true,
                    ...$movieData
                ];
            });

            $genres = $formattedMovies
                ->flatMap(fn($movie) => $movie['genres'] ?? [])
                ->unique('id')
                ->values()
                ->all();

            return response()->json([
                'data' => [
                    'movies' => $formattedMovies,
                    'genres' => $genres,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->handleGenericException($e, 'get favorite movies');
        }
    }

    private function addFavoriteFlag(Request $request, array $movies): array
    {
        $user = $request->user();
        if (!$user) {
            return $movies;
        }

        $favoriteMovieIds = $user->favoriteMovies()->pluck('movie_id');

        foreach ($movies as &$movie) {
            $movie['is_favorite'] = $favoriteMovieIds->contains($movie['id']);
        }

        return $movies;
    }

    public function getMoviesByGenre(int $genreId): JsonResponse
    {
        try {
            $movies = $this->tmdbService->getMovieByGenre($genreId);

            return response()->json([
                'data' =>  $movies,
            ]);
        } catch (\Exception $e) {
            return $this->handleGenericException($e, 'get favorite movies');
        }
    }
}
