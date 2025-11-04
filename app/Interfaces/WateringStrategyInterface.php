<?php

namespace App\Interfaces;

use App\Models\Plant;

interface WateringStrategyInterface
{
    /**
     * Calculate the number of days until the next watering for a plant.
     *
     * @param Plant $plant The Plant model (contains watering benchmarks)
     * @param array $weather Current weather data (temperature, humidity, precipitation, condition)
     * @param bool $needsWater Whether the weather service says watering is needed now
     * @return int Number of days until next watering (0 means no watering needed now)
     */
    public function calculateDaysUntilNextWatering(Plant $plant, array $weather = [], bool $needsWater = true): int;
}