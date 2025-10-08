<?php 

namespace App\Interfaces;

interface WeatherServiceInterface 
{
    public function fetchAndStoreWeatherData(): void;
    public function getWeatherForCity(string $city): array;
}