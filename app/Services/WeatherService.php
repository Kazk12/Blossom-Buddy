<?php
namespace App\Services;

use App\Interfaces\WeatherServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WeatherService implements WeatherServiceInterface
{
    protected $apiWeatherUrl = 'http://api.weatherapi.com/v1/forecast.json';
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.weatherapi.key');
    }

    public function fetchAndStoreWeatherData(): void
    {
        // Cette méthode n'est plus utilisée car nous utilisons le cache
    }

    public function getWeatherForCity(string $city): array
    {
        $cacheKey = "weather_data_{$city}";

        return Cache::remember($cacheKey, now()->addHours(2), function () use ($city) {
            $response = Http::get($this->apiWeatherUrl, [
                'key' => $this->apiKey,
                'q' => $city,
                'days' => 1,
                'aqi' => 'no'
            ]);

            if (!$response->successful()) {
                Log::error('Weather API request failed', [
                    'city' => $city,
                    'response' => $response->body()
                ]);
                return [
                    'needs_water' => true // Par défaut, on recommande d'arroser en cas d'erreur
                ];
            }

            $data = $response->json();
            $current = $data['current'];

            // Logique pour déterminer si la plante a besoin d'eau
            $needsWater = $this->determineIfNeedsWater(
                $current['temp_c'],
                $current['humidity'],
                $current['precip_mm'],
                $current['condition']['text']
            );

            return [
                'needs_water' => $needsWater,
                'current_weather' => [
                    'temperature' => $current['temp_c'],
                    'humidity' => $current['humidity'],
                    'precipitation' => $current['precip_mm'],
                    'condition' => $current['condition']['text']
                ]
            ];
        });
    }

    private function determineIfNeedsWater(float $temperature, float $humidity, float $precipitation, string $condition): bool
    {
        // Conditions qui suggèrent qu'il n'est pas nécessaire d'arroser
        if ($precipitation > 2.0) { // S'il a plu plus de 2mm
            return false;
        }

        if (str_contains(strtolower($condition), 'rain')) {
            return false;
        }

        // Conditions qui suggèrent qu'il faut arroser
        if ($temperature > 25 && $humidity < 60) {
            return true;
        }

        if ($temperature > 30) {
            return true;
        }

        // Par défaut, on suggère d'arroser si l'humidité est basse
        return $humidity < 50;
    }
}
