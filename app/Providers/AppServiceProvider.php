<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\Product\ProductServiceInterface;
use App\Services\ProductService;
use App\Repositories\Contracts\Product\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\Contracts\Product\CategoryRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Services\Contracts\Product\ImageStorageInterface;
use App\Services\ImageStorageService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(
                ImageStorageInterface::class,
                ImageStorageService::class
            );
        $this->app->bind(
                CategoryRepositoryInterface::class,
                CategoryRepository::class
            );

        $this->app->bind(
                ProductRepositoryInterface::class,
                ProductRepository::class
            );

        $this->app->bind(
            ProductServiceInterface::class,
            ProductService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
