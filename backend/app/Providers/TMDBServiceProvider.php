<?php

namespace App\Providers;

use App\Contracts\HttpClientInterface;
use App\Contracts\TMDBServiceInterface;
use App\Services\HttpClient;
use App\Services\TMDBService;
use Illuminate\Support\ServiceProvider;

class TMDBServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(HttpClientInterface::class, HttpClient::class);
        $this->app->bind(TMDBServiceInterface::class, TMDBService::class);
    }

    public function boot(): void
    {
        //
    }
}
