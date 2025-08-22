<?php

namespace App\Http\Controllers;

use App\Services\MovieService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MovieController extends Controller
{
    public function __construct(private readonly MovieService $service)
    {
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1',
            'page' => 'nullable|integer|min:1',
            'language' => 'nullable|string',
        ]);

        try {
            $data = $this->service->search(
                query: $request->string('q'),
                page: (int) $request->input('page', 1),
                language: $request->input('language')
            );
            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error('Search endpoint failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to search movies'], 500);
        }
    }

    public function genres(Request $request): JsonResponse
    {
        $request->validate([
            'language' => 'nullable|string',
        ]);

        try {
            $data = $this->service->genres(language: $request->input('language'));
            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error('Genres endpoint failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch genres'], 500);
        }
    }

    public function trending(Request $request, string $window = 'day'): JsonResponse
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
            'language' => 'nullable|string',
        ]);

        try {
            $data = $this->service->trending(
                window: $window,
                page: (int) $request->input('page', 1),
                language: $request->input('language')
            );
            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error('Trending endpoint failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch trending movies'], 500);
        }
    }
}