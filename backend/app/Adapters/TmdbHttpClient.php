<?php

namespace App\Adapters;

use Illuminate\Support\Facades\Http;

class TmdbHttpClient
{
    public function __construct(
        private readonly string $token,
        private readonly string $baseUrl,
    ) {
    }

    public function get(string $path, array $query = []): array
    {
        $response = Http::withToken($this->token)
            ->acceptJson()
            ->get(rtrim($this->baseUrl, '/').'/'.ltrim($path, '/'), $query);

        if (!$response->successful()) {
            return [
                'ok' => false,
                'status' => $response->status(),
                'body' => $response->json(),
            ];
        }

        return [
            'ok' => true,
            'status' => $response->status(),
            'body' => $response->json(),
        ];
    }
}