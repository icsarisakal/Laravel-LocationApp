<?php

namespace App\Providers;

use App\Interfaces\AuthRepostoryInterface;
use App\Interfaces\LocationRepositoryInterface;
use App\Repositories\AuthRepository;
use App\Repositories\LocationRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(LocationRepositoryInterface::class,LocationRepository::class);
        $this->app->bind(AuthRepostoryInterface::class,AuthRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
