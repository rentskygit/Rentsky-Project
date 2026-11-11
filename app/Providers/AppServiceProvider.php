<?php

namespace App\Providers;

use App\Services\Contracts\Property\PropertyImageStorageInterface;
use App\Services\Contracts\Property\VideoStorageInterface;
use App\Services\PropertyImageStorageService;
use App\Services\VideoStorageService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PropertyImageStorageInterface::class,
            PropertyImageStorageService::class
        );

        $this->app->bind(
            VideoStorageInterface::class,
            VideoStorageService::class
        );
    }

    public function boot(): void
    {
        //
    }
}