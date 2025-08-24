<?php

namespace App\Exceptions;

use Exception;

class TMDBException extends Exception
{
    protected $statusCode;
    protected $responseData;

    public function __construct(string $message = "", int $code = 0, ?Exception $previous = null, int $statusCode = 0, array $responseData = [])
    {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
        $this->responseData = $responseData;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponseData(): array
    {
        return $this->responseData;
    }

    public static function fromResponse(int $statusCode, array $responseData): self
    {
        $message = $responseData['status_message'] ?? 'TMDB API Error';
        $code = $responseData['status_code'] ?? $statusCode;

        return new self($message, $code, null, $statusCode, $responseData);
    }

    public static function networkError(string $message, int $code = 0): self
    {
        return new self("Network Error: {$message}", $code, null, 0, []);
    }

    public static function invalidResponse(string $message): self
    {
        return new self("Invalid Response: {$message}", 0, null, 0, []);
    }
}
