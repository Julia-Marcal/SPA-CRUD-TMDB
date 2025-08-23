<?php

use App\Http\Controllers\MoviesController;
use Illuminate\Support\Facades\Route;

Route::prefix('movies')->group(function () {
    Route::get('/search', [MoviesController::class, 'search']);
    Route::get('/genres', [MoviesController::class, 'genres']);
    Route::get('/trending', [MoviesController::class, 'trending']);
    Route::get('/{movieId}', [MoviesController::class, 'show']);
});
