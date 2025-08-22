<?php

use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;

Route::prefix('movies')->group(function () {
    Route::get('search', [MovieController::class, 'search']);
    Route::get('genres', [MovieController::class, 'genres']);
    Route::get('trending/{window?}', [MovieController::class, 'trending']);
});