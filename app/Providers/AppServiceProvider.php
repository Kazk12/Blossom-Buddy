<?php

namespace App\Providers;

use App\Interfaces\PlantsServiceInterface;
use App\Interfaces\WeatherServiceInterface;
use App\Services\PlantService;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PlantsServiceInterface::class, PlantService::class);
        $this->app->bind(WeatherServiceInterface::class, WeatherService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
