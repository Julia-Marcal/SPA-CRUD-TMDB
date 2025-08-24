<?php

use App\Http\Controllers\MoviesController;
use Illuminate\Support\Facades\Route;

Route::prefix('movies')->group(function () {
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/search', [MoviesController::class, 'search']);
        Route::get('/genres', [MoviesController::class, 'genres']);
        Route::get('/trending', [MoviesController::class, 'trending']);

        Route::get('/favorites', [MoviesController::class, 'getFavoriteMovies']);
        Route::post('/favorites/{movieId}', [MoviesController::class, 'addFavorite']);
        Route::delete('/favorites/{movieId}', [MoviesController::class, 'removeFromFavorite']);

        Route::get('/genre/{genreId}', [MoviesController::class, 'getMoviesByGenre']);

        Route::get('/{movieId}', [MoviesController::class, 'show']);

    });
});
