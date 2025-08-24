<?php

namespace App\Http\Controllers\Traits;

use App\Exceptions\TMDBException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait HandlesControllerExceptions
{
    protected function handleTMDBException(TMDBException $e, string $context, $movieId = null): JsonResponse
    {
        $logData = [
            'error' => $e->getMessage(),
            'status_code' => $e->getStatusCode(),
        ];
        if ($movieId !== null) {
            $logData['movie_id'] = $movieId;
        }
        Log::error("TMDB API error in {$context}", $logData);

        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'status_code' => $e->getStatusCode() ?: 500,
        ], $e->getStatusCode() ?: 500);
    }

    protected function handleGenericException(\Exception $e, string $context, $movieId = null): JsonResponse
    {
        $logData = [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ];
        if ($movieId !== null) {
            $logData['movie_id'] = $movieId;
        }
        Log::error("Unexpected error in {$context}", $logData);

        return response()->json([
            'success' => false,
            'error' => 'An unexpected error occurred',
            'status_code' => 500,
        ], 500);
    }
}
