<?php 

namespace App\Strategy;

use App\Interfaces\WateringStrategyInterface;
use App\Models\Plant;

class DefaultWateringStrategy implements WateringStrategyInterface
{
    public function calculateDaysUntilNextWatering(Plant $plant, array $weather = [], bool $needsWater = true): int
    {
        // If weather indicates no watering is needed right now
        if (!$needsWater) {
            return 0; // 0 days = no watering needed now
        }

        // Try to get a base frequency from the plant's benchmark data
        $benchmark = $plant->watering_general_benchmark ?? [];
        $baseDays = 7; // sensible default

        if (is_array($benchmark)) {
            // Common key names that might exist in the JSON benchmark
            if (isset($benchmark['frequency_days']) && is_numeric($benchmark['frequency_days'])) {
                $baseDays = (int) $benchmark['frequency_days'];
            } elseif (isset($benchmark['watering_period']) && is_numeric($benchmark['watering_period'])) {
                $baseDays = (int) $benchmark['watering_period'];
            } elseif (isset($benchmark['base']) && is_numeric($benchmark['base'])) {
                $baseDays = (int) $benchmark['base'];
            }
        }

        $days = $baseDays;

        // Apply weather-based adjustments
        $temp = $weather['temperature'] ?? null;
        $humidity = $weather['humidity'] ?? null;
        $precip = $weather['precipitation'] ?? null;
        $condition = strtolower($weather['condition'] ?? '');

        // If it recently precipitated significantly, postpone watering
        if (is_numeric($precip) && $precip > 2.0) {
            $days += 2; // delay a bit after rainfall
        }

        // If condition mentions rain or drizzle, delay watering
        if (str_contains($condition, 'rain') || str_contains($condition, 'drizzle')) {
            $days += 2;
        }

        // Hot and dry -> water sooner
        if (is_numeric($temp) && is_numeric($humidity)) {
            if ($temp > 30) {
                $days = max(1, $days - 3);
            } elseif ($temp > 25 && $humidity < 60) {
                $days = max(1, $days - 2);
            } elseif ($humidity < 40) {
                $days = max(1, $days - 1);
            }
        }

        // Ensure at least 0 days (0 means no watering needed now) and integer
        return (int) max(0, $days);
    }
}