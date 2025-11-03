<?php

namespace App\Services;

use App\Interfaces\PlantsServiceInterface;
use App\Interfaces\LoggingServiceInterface;

class PlantServiceLoggingDecorator implements PlantsServiceInterface
{
    private PlantsServiceInterface $plantsService;
    private LoggingServiceInterface $loggingService;

    public function __construct(
        PlantsServiceInterface $plantsService,
        LoggingServiceInterface $loggingService
    ) {
        $this->plantsService = $plantsService;
        $this->loggingService = $loggingService;
    }

    public function fetchAndStorePlantsData(): void
    {
        $this->loggingService->logInfo("Starting to fetch and store plants data.");
        $this->plantsService->fetchAndStorePlantsData();
        $this->loggingService->logInfo("Finished fetching and storing plants data.");
    }

    public function searchPlantByName(string $name, int $maxRetries = 3): array
    {
        $this->loggingService->logInfo("Searching for plant by name: {$name} with max retries: {$maxRetries}.");
        $result = $this->plantsService->searchPlantByName($name, $maxRetries);
        $this->loggingService->logInfo("Completed search for plant by name: {$name}.");
        return $result;
    }

    public function checkAndCompleteData(string $name): ?array
    {
        $this->loggingService->logInfo("Checking and completing data for plant: {$name}.");
        $result = $this->plantsService->checkAndCompleteData($name);
        $this->loggingService->logInfo("Completed checking and completing data for plant: {$name}.");
        return $result;
    }
}