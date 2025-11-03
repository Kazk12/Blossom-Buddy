<?php 

namespace App\Strategy;

use App\Interfaces\WateringStrategyInterface;

class DefaultWateringStrategy implements WateringStrategyInterface
{
    public function calculateDaysUntilNextWatering(array $plantData): int
    {
        return 7;
    }
}