<?php

namespace App\Services;


use App\Interfaces\PlantsServiceInterface;
use App\Models\Plant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Service appelé dans la commande FetchPlants
class PlantService implements PlantsServiceInterface
{
    protected $apiUrl = 'https://perenual.com/api/v2/species/details';

    
    public function fetchAndStorePlantsData(): void
    {
        for ($id = 1; $id <= 3; $id++) {
            $plantData = $this->fetchPlantData($id);

            if ($plantData && !empty($plantData)) {
                $plantDataFiltered = $this->filterPlantData($plantData);
                $this->storePlantData($plantDataFiltered);
            }
        }
    }

    /**
     * Fetches plant data from the Perenual API.
     * @param int $id
     * @return array
     */
    private function fetchPlantData(int $id): array
    {
        $apiKey = env('PERENUAL_API_KEY');

        $response = Http::withoutVerifying()->get("{$this->apiUrl}/{$id}", [
            'key' => $apiKey
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            Log::error("Failed to fetch plant with ID {$id}: " . $response->body());
            return [];
        }
    }

    /**
     * Filters plant data to keep only the relevant fields.
     * @param array $plantData
     * @return array
     */
    private function filterPlantData(array $plantData): array
    {
        return [
            'api_id' => $plantData['id'],
            'common_name' => $plantData['common_name'],
            'watering_general_benchmark' => $plantData['watering_general_benchmark'],
            'watering' => $plantData['watering'] ?? null,
            'watering_period' => $plantData['watering_period'] ?? null,
            'flowers' => (bool)($plantData['flowers'] ?? false),
            'fruits' => (bool)($plantData['fruits'] ?? false),
            'leaf' => (bool)($plantData['leaf'] ?? false),
            'growth_rate' => $plantData['growth_rate'] ?? null,
            'maintenance' => $plantData['maintenance'] ?? null,
        ];
    }

   
    private function storePlantData(array $plantData): void
    {
        // Utilisation de upsert pour éviter les doublons basés sur api_id
        Plant::updateOrCreate(
            ['api_id' => $plantData['api_id']],
            $plantData
        );
    }
}