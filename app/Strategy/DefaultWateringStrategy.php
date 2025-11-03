<?php 

namespace App\Strategy;

use App\Interfaces\WateringStrategyInterface;

class DefaultWateringStrategy implements WateringStrategyInterface
{
    public function calculateDaysUntilNextWatering(array $plantData): int
    {
        // Basic logic: water every 7 days
        return 7;
    }
}