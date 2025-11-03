<?php

namespace App\Providers;

use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\PlantRepositoryInterface;
use App\Interfaces\PlantsServiceInterface;
use App\Interfaces\WeatherServiceInterface;
use App\Interfaces\WateringStrategyInterface;
use App\Interfaces\LoggingServiceInterface;
use App\Repositories\AuthRepository;
use App\Repositories\PlantRepository;
use App\Services\PlantService;
use App\Services\WeatherService;
use App\Services\LoggingService;
use App\Services\PlantServiceLoggingDecorator;
use App\Strategy\DefaultWateringStrategy;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoggingServiceInterface::class, LoggingService::class);
        $this->app->bind(PlantsServiceInterface::class, function ($app) {
            return new PlantServiceLoggingDecorator(
                $app->make(PlantService::class),
                $app->make(LoggingServiceInterface::class)
            );
        });
        $this->app->bind(WeatherServiceInterface::class, WeatherService::class);
        $this->app->bind(WateringStrategyInterface::class, DefaultWateringStrategy::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(PlantRepositoryInterface::class, PlantRepository::class);
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
