<?php

namespace App\Exceptions;

use Exception;

class TmdbException extends Exception
{
    public static function withStatus(int $code, string $message): self
    {
        return new self("TMDB error ({$code}): {$message}", $code);
    }
}