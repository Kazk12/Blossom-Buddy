<?php

namespace App\Strategy;

use App\Interfaces\WateringStrategyInterface;
use App\Models\Plant;

/**
 * Stratégie pour les plantes tropicales (besoin d'humidité plus fréquente).
 */
class TropicalWateringStrategy implements WateringStrategyInterface
{
    public function calculateDaysUntilNextWatering(Plant $plant, array $weather = [], bool $needsWater = true): int
    {
        // Si la météo indique qu'il n'est pas nécessaire d'arroser
        if (!$needsWater) {
            return 0;
        }

        $benchmark = $plant->watering_general_benchmark ?? [];
        $baseDays = 3; // valeur par défaut pour plantes tropicales

        if (is_array($benchmark)) {
            if (isset($benchmark['frequency_days']) && is_numeric($benchmark['frequency_days'])) {
                $baseDays = (int) $benchmark['frequency_days'];
            }
        }

        // Taille du pot : petits pots sèchent plus vite
        $potSize = $benchmark['pot_size'] ?? null;
        if ($potSize === 'small') {
            $baseDays = max(1, $baseDays - 1);
        } elseif ($potSize === 'large') {
            $baseDays += 1;
        }

        // Sol riche en tourbe retient mieux l'humidité -> espacer un peu
        $soil = strtolower($benchmark['soil_type'] ?? '');
        if (str_contains($soil, 'peat') || str_contains($soil, 'tourbe')) {
            $baseDays += 1;
        }

        // Si la météo est humide, on peut espacer
        $humidity = $weather['humidity'] ?? null;
        if (is_numeric($humidity) && $humidity > 70) {
            $baseDays += 1;
        }

        // Conditions chaudes et sèches -> arroser plus souvent
        $temp = $weather['temperature'] ?? null;
        if (is_numeric($temp) && $temp > 28 && is_numeric($humidity) && $humidity < 60) {
            $baseDays = max(1, $baseDays - 1);
        }

        return (int) max(0, $baseDays);
    }
}
