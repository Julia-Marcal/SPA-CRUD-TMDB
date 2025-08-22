<?php

namespace App\Factories;

use App\Adapters\TmdbHttpClient;
use App\Adapters\TmdbMovieAdapter;
use App\Contracts\MovieProviderInterface;
use App\Decorators\CachedMovieProvider;
use App\Decorators\LoggedMovieProvider;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

class MovieProviderFactory
{
    public static function make(array $config): MovieProviderInterface
    {
        $client = new TmdbHttpClient(
            token: $config['token'],
            baseUrl: $config['base_url']
        );

        $adapter = new TmdbMovieAdapter(
            client: $client,
            defaultLanguage: $config['language'],
        );

        $provider = new LoggedMovieProvider($adapter);
        $provider = new CachedMovieProvider($provider, (int) ($config['cache_ttl'] ?? 600));

        return $provider;
    }
}