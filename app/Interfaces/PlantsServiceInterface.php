<?php 

namespace App\Interfaces;

interface PlantsServiceInterface 
{
    /**
     * Fetch data from remote API and store in local DB.
     */
    public function fetchAndStorePlantsData(): void;

    /**
     * Search plants by name (DB, cache, then API).
     * Returns an array with keys: source and results.
     */
    public function searchPlantByName(string $name, int $maxRetries = 3): array;

    /**
     * Ensure a plant's data is complete (DB or API) and return it.
     */
    public function checkAndCompleteData(string $name): ?array;
}
