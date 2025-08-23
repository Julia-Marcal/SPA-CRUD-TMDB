<?php

namespace App\Services;

use App\Contracts\HttpClientInterface;
use App\Exceptions\TMDBException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class HttpClient implements HttpClientInterface
{
    protected Client $client;
    protected string $baseUrl;
    protected string $bearerToken;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.base_url');
        $this->bearerToken = config('services.tmdb.bearer_token');
        $this->timeout = config('services.tmdb.timeout', 30);

        $this->client = new Client([
            'timeout' => $this->timeout,
            'headers' => [
                'Authorization' => "Bearer {$this->bearerToken}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function get(string $endpoint, array $queryParams = []): array
    {
        try {
            $fullUrl = $this->baseUrl . '/' . ltrim($endpoint, '/');

            $response = $this->client->get($fullUrl, [
                'query' => $queryParams,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw TMDBException::invalidResponse('Invalid JSON response');
            }

            return $data;
        } catch (GuzzleException $e) {
            Log::error('TMDB API Request Failed', [
                'endpoint' => $endpoint,
                'query_params' => $queryParams,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            if ($e->getCode() >= 400 && $e->getCode() < 500) {
                try {
                    if (property_exists($e, 'response') && $e->response) {
                        $responseData = json_decode($e->response->getBody()->getContents(), true) ?? [];
                        throw TMDBException::fromResponse($e->getCode(), $responseData);
                    }
                } catch (\Exception $responseException) {
                }
            }

            throw TMDBException::networkError($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            Log::error('Unexpected error in TMDB API request', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            throw TMDBException::networkError($e->getMessage(), $e->getCode());
        }
    }

    public function post(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->client->post($endpoint, [
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('TMDB API POST Request Failed', [
                'endpoint' => $endpoint,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            throw TMDBException::networkError($e->getMessage(), $e->getCode());
        }
    }

    public function put(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->client->put($endpoint, [
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('TMDB API PUT Request Failed', [
                'endpoint' => $endpoint,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            throw TMDBException::networkError($e->getMessage(), $e->getCode());
        }
    }

    public function delete(string $endpoint): array
    {
        try {
            $response = $this->client->delete($endpoint);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('TMDB API DELETE Request Failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            throw TMDBException::networkError($e->getMessage(), $e->getCode());
        }
    }
}
