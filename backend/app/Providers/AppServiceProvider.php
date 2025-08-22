<?php

namespace App\Providers;

use App\Contracts\MovieProviderInterface;
use App\Factories\MovieProviderFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MovieProviderInterface::class, function ($app) {
            $config = config('services.tmdb');
            return MovieProviderFactory::make($config);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
